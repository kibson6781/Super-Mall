from machine import Pin, ADC, Timer
import gc
import weather
import urequests
import time
import os
import network
import json
import _thread
from constants import PLANTS
from dht import DHT11

# CONSTANTS
DRY = 30710
WET = 19641
RANGE = 11069

SOIL = ADC(Pin(26))
PUMP = Pin(16, Pin.OUT)
TEMPERATURE_SENSOR = DHT11(Pin(17))

DEV_ID = '12345678'
DEV_PASS = 'admin@123'
FILE_NAME = 'data.json'
JSON_PLANT_NAME = 'PLANT_NAME'
JSON_GROWTH_STAGE = 'GROWTH_STAGE'
JSON_DAYS_FOR_NEXT_STAGE = 'DAYS_FOR_NEXT_STAGE'
JSON_TIME = 'TIME'
SERVER_BASE_URL = "http://192.168.18.108/irrigation/update_data"
STAGES = {0: 'Germination', 1: 'Vegetative', 2: 'Reproductive', 3: 'Grain-filling', 4: 'Maturation'}
FINAL_GROWTH_STAGE = 4

IRRIGATING = False
TIMESTAMP = 0
WAIT_TIME = 60 * 10  # 10 minutes

# For Recovery
if FILE_NAME in os.listdir():
    with open(FILE_NAME, 'r+') as file:
        data = json.load(file)
        PLANT_NAME = data[JSON_PLANT_NAME]
        GROWTH_STAGE = data[JSON_GROWTH_STAGE]
        DAYS_FOR_NEXT_STAGE = data[JSON_DAYS_FOR_NEXT_STAGE]
        TIME = data[JSON_TIME]

    time_diff_days = (time.time() - TIME) / (60 * 60 * 24)

    if time_diff_days >= 1:
        DAYS_FOR_NEXT_STAGE -= time_diff_days
        if DAYS_FOR_NEXT_STAGE <= 0:
            while DAYS_FOR_NEXT_STAGE <= 0:
                if GROWTH_STAGE != FINAL_GROWTH_STAGE:
                    GROWTH_STAGE += 1
                    if PLANTS[PLANT_NAME][GROWTH_STAGE] is None:
                        # Harvest
                        pass
                else:
                    # Harvest
                    pass
            PLANT_UPPER, PLANT_LOWER, NEXT_STAGE = PLANTS[PLANT_NAME][GROWTH_STAGE]
            DAYS_FOR_NEXT_STAGE += NEXT_STAGE
        else:
            PLANT_UPPER, PLANT_LOWER, NEXT_STAGE = PLANTS[PLANT_NAME][GROWTH_STAGE]
            DAYS_FOR_NEXT_STAGE += NEXT_STAGE
        Timer(-1).init(mode=Timer.ONE_SHOT, period=(time.time() - TIME) * 1000, callback=update_values)
else:
    PLANT_NAME = 'sugarcane'
    GROWTH_STAGE = 0
    PLANT_LOWER, PLANT_UPPER, DAYS_FOR_NEXT_STAGE = PLANTS[PLANT_NAME][GROWTH_STAGE]
    PLANT_AGE = 2

TOLERANCE = 5
BIASED_UPPER = PLANT_UPPER + TOLERANCE
BIASED_LOWER = PLANT_LOWER - TOLERANCE
TEMP_LOWER, TEMP_UPPER = PLANTS[PLANT_NAME]['temp']

# FUNCTIONS
def connect_to_wifi():
    SSID = 'ssid'
    PASS = 'password'
    sta_if = network.WLAN(network.STA_IF)
    if not sta_if.isconnected():
        print('Connecting to network...')
        sta_if.active(True)
        sta_if.connect(SSID, PASS)
        while not sta_if.isconnected():
            pass
    print('Network config:', sta_if.ifconfig())

def calculate_moisture(n):
    total = 0
    for _ in range(n):
        total += SOIL.read_u16()
        time.sleep(0.01)
    return total / n

def get_moisture_percentage(moisture):
    inv_percent = (moisture - WET) / RANGE * 100
    return (100 - inv_percent)

def irrigate():
    """To water plants by starting the pump"""
    global IRRIGATING, TIMESTAMP
    PUMP.value(1)
    IRRIGATING = True

    moisture = calculate_moisture(10)
    percentage = get_moisture_percentage(moisture)
    print("Pump on")
    while not percentage > BIASED_UPPER:
        moisture = calculate_moisture(10)
        percentage = get_moisture_percentage(moisture)
        print(percentage)

    PUMP.value(0)
    print("Pump off")
    TIMESTAMP = time.time()

def update_values(t):
    global DAYS_FOR_NEXT_STAGE, PLANT_LOWER, PLANT_UPPER, BIASED_UPPER, BIASED_LOWER, GROWTH_STAGE

    DAYS_FOR_NEXT_STAGE -= 1
    if DAYS_FOR_NEXT_STAGE == 0:
        if GROWTH_STAGE == FINAL_GROWTH_STAGE or PLANTS[PLANT_NAME][GROWTH_STAGE + 1] is None:
            # Time to harvest
            pass
        else:
            # Next growth stage of plant
            GROWTH_STAGE += 1
            PLANT_LOWER, PLANT_UPPER, DAYS_FOR_NEXT_STAGE = PLANTS[PLANT_NAME][GROWTH_STAGE]
            BIASED_LOWER, BIASED_UPPER = [PLANT_LOWER - TOLERANCE, PLANT_UPPER + TOLERANCE]
        log_data()

def log_data():
    """Log important data for system recovery in case of power failure"""
    with open(FILE_NAME, 'w') as file:
        data = {
            JSON_PLANT_NAME: PLANT_NAME,
            JSON_GROWTH_STAGE: GROWTH_STAGE,
            JSON_DAYS_FOR_NEXT_STAGE: DAYS_FOR_NEXT_STAGE,
            JSON_TIME: time.time()
        }
        json.dump(data, file)

thread_lock = _thread.allocate_lock()

def send_data():
    thread_lock.acquire()
    print('Sending data')
    TEMPERATURE_SENSOR.measure()
    temp = TEMPERATURE_SENSOR.temperature()
    humidity = TEMPERATURE_SENSOR.humidity()

    moisture = calculate_moisture(10)
    percentage = get_moisture_percentage(moisture)
    rain_chance = 0  # Placeholder for get_rain_data()

    print(rain_chance)

    post_data = f'device-id={DEV_ID}&password={DEV_PASS}&temperature={temp}&humidity={humidity}&soil_moisture={percentage}&growth_stage={STAGES[GROWTH_STAGE]}&rain_chance={rain_chance}'
    response = urequests.post(SERVER_BASE_URL, headers={'Content-Type': 'application/x-www-form-urlencoded'}, data=post_data)
    response.close()
    gc.collect()
    print(response.text)

    post_data_json = json.dumps({
        'device-id': DEV_ID,
        'password': DEV_PASS,
        'temperature': temp,
        'humidity': humidity,
        'soil_moisture': percentage,
        'growth_stage': STAGES[GROWTH_STAGE],
        'rain_chance': rain_chance
    }, separators=(', ', ':'))

    res = urequests.post(SERVER_BASE_URL, headers={'Content-Type': 'application/json'}, data=post_data_json)
    print(res.text)
    thread_lock.release()

def start_thread(t):
    _thread.start_new_thread(send_data, ())

connect_to_wifi()
Timer(-1).init(period=5 * 1000, callback=start_thread)

# MAIN LOOP
while True:
    moisture = calculate_moisture(10)
    percentage = get_moisture_percentage(moisture)
    hour_of_day = time.localtime()[3]

    if (percentage < PLANT_LOWER and 18 < hour_of_day < 10) or (percentage < BIASED_LOWER):
        print('Irrigating')
        irrigate()
    elif IRRIGATING and time.time() - TIMESTAMP > WAIT_TIME:
        if percentage < PLANT_UPPER:
            print('Reirrigating')
            irrigate()
        else:
            IRRIGATING = False
    print(percentage)
    time.sleep(0.5)
