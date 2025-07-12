// resources/js/firebase.js
import { initializeApp } from 'firebase/app'
import { getAuth } from 'firebase/auth'

const firebaseConfig = {
    apiKey: "AIzaSyDhIoSQZTQvyM9NVxBC93srednwLp7wAXE",
    authDomain: "virtualcardprovider-c0758.firebaseapp.com",
    projectId: "virtualcardprovider-c0758",
    storageBucket: "virtualcardprovider-c0758.firebasestorage.app",
    messagingSenderId: "645228203839",
    appId: "1:645228203839:web:c419d72c9baa03dade0d8a",
    measurementId: "G-CLEDE0PZMP"
}

const app = initializeApp(firebaseConfig)
const auth = getAuth(app)
auth.useDeviceLanguage()

export { auth }
