self.addEventListener("install", function (event) {
    self.skipWaiting();
});

self.addEventListener("activate", function (event) {
    event.waitUntil(self.clients.claim());
});

self.addEventListener("fetch", function (event) {
    event.respondWith(
        caches.match(event.request).then(function (response) {
            return response || fetch(event.request);
        })
    );
});

self.addEventListener("push", function (event) {
    let data = {};
    try {
        data = event.data.json();
    } catch (e) {
        data = {
            title: "Notifikasi",
            body: event.data ? event.data.text() : "",
        };
    }
    const options = {
        body: data.body || "",
        icon: "/absensioptik/public/image/optik-melati.png",
        badge: "/absensioptik/public/image/optik-melati.png",
        vibrate: [200, 100, 200],
        data: data.url ? { url: data.url } : {},
    };
    event.waitUntil(
        self.registration.showNotification(data.title || "Notifikasi", options)
    );
});

self.addEventListener("notificationclick", function (event) {
    event.notification.close();
    if (event.notification.data && event.notification.data.url) {
        event.waitUntil(clients.openWindow(event.notification.data.url));
    }
});
