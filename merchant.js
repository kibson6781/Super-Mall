import { db } from './firebase-config.js';
import { collection, addDoc, getDocs } from "firebase/firestore";

// Add product functionality
document.getElementById('add-product-form').addEventListener('submit', async (event) => {
    event.preventDefault();
    const name = document.getElementById('product-name').value;
    const description = document.getElementById('product-description').value;
    const price = document.getElementById('product-price').value;
    const imageUrl = document.getElementById('product-image').value;

    try {
        await addDoc(collection(db, "products"), { name, description, price, imageUrl });
        alert("Product added successfully!");
        document.getElementById('add-product-form').reset();
    } catch (e) {
        console.error("Error adding product: ", e);
    }
});

// Display products on the homepage
async function displayProducts() {
    const productList = document.getElementById('product-list');
    const querySnapshot = await getDocs(collection(db, "products"));
    querySnapshot.forEach((doc) => {
        const product = doc.data();
        const productItem = `
            <div class="product-item">
                <img src="${product.imageUrl}" alt="${product.name}">
                <h3>${product.name}</h3>
                <p>${product.description}</p>
                <p>$${product.price}</p>
                <button onclick="viewProduct('${doc.id}')">View Details</button>
            </div>
        `;
        productList.innerHTML += productItem;
    });
}

displayProducts();

window.viewProduct = function(productId) {
    localStorage.setItem('selectedProductId', productId);
    window.location.href = 'product-details.html';
}
