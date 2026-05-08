// resources/js/firebase.js
import { initializeApp } from "firebase/app";
import { getAuth } from "firebase/auth";
import { getFirestore } from "firebase/firestore";

// ✅ Correct Firebase config (double-check projectId)
const firebaseConfig = {
  apiKey: "AIzaSyDZBGNT1c26J7JLnthWAX2R4QCqKgMcah8",
  authDomain: "tangkonek-b8a90.firebaseapp.com",
  projectId: "tangkonek-b8a90",
  storageBucket: "tangkonek-b8a90.appspot.com", // ⚠️ ito ay dapat ".appspot.com" (not "firebasestorage.app")
  messagingSenderId: "98421445994",
  appId: "1:98421445994:web:15c22280608fd913d5c113",
  measurementId: "G-E26R7TDJ4E",
};

// ✅ Initialize Firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const db = getFirestore(app);

// ✅ Export for use
export { auth, db };
