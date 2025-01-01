<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>台灣魚群分佈地圖</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 1000px;
            width: 100%;
        }
    </style>
</head>
<body>

    <h1>台灣魚群分佈地圖</h1>
    <div id="map"></div>

    <!-- 引入 Leaflet.js -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // 初始化地圖，中心點設為台灣
        var map = L.map('map').setView([23.6978, 120.9605], 7);  // 台灣中心點經緯度

        // 加入 OpenStreetMap 圖層
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // 假設魚群數據是從後端獲取的
        var fishData = [
            {
                "location": "台北港",
                "lat": 25.1514,
                "lng": 121.7691,
                "amount": 150
            },
            {
                "location": "高雄港",
                "lat": 22.6163,
                "lng": 120.2725,
                "amount": 200
            },
            {
                "location": "花蓮港",
                "lat": 23.9785,
                "lng": 121.6121,
                "amount": 120
            }
        ];

        // 在地圖上標示魚群位置
        fishData.forEach(function(fish) {
            var marker = L.marker([fish.lat, fish.lng]).addTo(map);
            marker.bindPopup(
                "<b>" + fish.location + "</b><br>" +
                "魚群數量: " + fish.amount + " 條"
            );
        });
    </script>

</body>
</html>
