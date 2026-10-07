import { addDoc, collection, onSnapshot } from "firebase/firestore";

// Gửi tin nhắn
addDoc(collection(db, "messages"), {
  from: myId,
  to: friendId,
  text: message,
  createdAt: Date.now()
});

// Nhận tin real-time
onSnapshot(collection(db, "messages"), (snapshot) => {
  snapshot.docChanges().forEach(change => {
    if (change.type === "added") {
      console.log("Tin nhắn mới:", change.doc.data());
    }
  });
});
