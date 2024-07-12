<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <style>
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        #map {
            height: 100vh; /* Tinggi peta mengambil tinggi seluruh viewport */
        }

        .location-item {
            cursor: pointer;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .location-item:hover {
            background-color: #f0f0f0;
        }

        .location-list {
            max-height: 600px;
            overflow-y: auto;
            border-right: 1px solid #ccc;
            padding: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 location-list">
                <h4>Daftar Lokasi</h4>
                <?php if (session()->get('logged_in')): ?>
                <button type="button" class="btn btn-primary mb-3" id="tambahLocationBtn" data-toggle="modal" data-target="#locationModal">Tambah Lokasi</button>
                <a href="logout" class="btn btn-danger mb-3">Logout</a>
                <?php else: ?>
                <a href="/map" class="btn btn-primary mb-3">Login</a>
                <?php endif; ?>
                <div id="location-list"></div>
            </div>
            <div class="col-md-9">
                <div id="map"></div>
            </div>
        </div>
    </div>

    <!-- Modal untuk tambah/edit lokasi -->
    <div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="locationModalLabel">Tambah Lokasi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="locationForm">
                    <div class="modal-body">
                        <input type="hidden" id="locationId" name="locationId">
                        <div class="form-group">
                            <label for="nama">Nama Lokasi:</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label for="latitude">Latitude:</label>
                            <input type="text" class="form-control" id="latitude" name="latitude" required>
                        </div>
                        <div class="form-group">
                            <label for="longitude">Longitude:</label>
                            <input type="text" class="form-control" id="longitude" name="longitude" required>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan:</label>
                            <textarea class="form-control" id="keterangan" name="keterangan"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    var map = L.map('map').setView([-3.8033144666164134, 114.76828788652068], 13); // Set center dan zoom level

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    var markers = {};

    // Function to load locations
    function loadLocations() {
        fetch('/map/getLocations') // Adjust URL to match your controller and method
            .then(response => response.json())
            .then(data => {
                var locationList = document.getElementById('location-list');
                locationList.innerHTML = ''; // Clear previous list

                data.forEach(location => {
                    // Create list item for each location
                    var locationItem = document.createElement('div');
                    locationItem.className = 'location-item d-flex justify-content-between align-items-center';
                    locationItem.innerHTML = `<span>${location.nama}</span>
                        <?php if (session()->get('logged_in')): ?>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary editLocationBtn" data-toggle="modal" data-target="#locationModal" data-id="${location.id_lokasi}" data-nama="${location.nama}" data-lat="${location.latitude}" data-lng="${location.longitude}" data-keterangan="${location.keterangan}">Edit</button>
                            <button type="button" class="btn btn-sm btn-danger deleteLocationBtn" data-id="${location.id_lokasi}">Hapus</button>
                        </div>
                        <?php endif; ?>`;
                    locationItem.setAttribute('data-id', location.id_lokasi); // Set data-id attribute
                    locationItem.setAttribute('data-lat', location.latitude); // Set data-lat attribute
                    locationItem.setAttribute('data-lng', location.longitude); // Set data-lng attribute
                    locationList.appendChild(locationItem);

                    // Add marker to the map
                    var marker = L.marker([location.latitude, location.longitude])
                        .addTo(map)
                        .bindPopup('<b>' + location.nama + '</b><br>' + location.keterangan);

                    markers[location.id_lokasi] = marker;
                });

                // Add event listener for location items
                document.querySelectorAll('.location-item').forEach(item => {
                    item.addEventListener('click', function() {
                        var id = this.getAttribute('data-id');
                        var lat = this.getAttribute('data-lat');
                        var lng = this.getAttribute('data-lng');
                        map.setView([lat, lng], 15); // Zoom to location
                        if (markers[id]) {
                            markers[id].openPopup(); // Open popup of the corresponding marker
                        }
                    });
                });

                // Add event listener for edit buttons
                document.querySelectorAll('.editLocationBtn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        var id = this.getAttribute('data-id');
                        var nama = this.getAttribute('data-nama');
                        var lat = this.getAttribute('data-lat');
                        var lng = this.getAttribute('data-lng');
                        var keterangan = this.getAttribute('data-keterangan');

                        // Set form values
                        $('#locationId').val(id);
                        $('#nama').val(nama);
                        $('#latitude').val(lat);
                        $('#longitude').val(lng);
                        $('#keterangan').val(keterangan);
                    });
                });

                // Add event listener for delete buttons
                document.querySelectorAll('.deleteLocationBtn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        var id = this.getAttribute('data-id');
                        if (confirm('Apakah Anda yakin ingin menghapus lokasi ini?')) {
                            // Send DELETE request to remove location
                            fetch(`/map/deleteLocation/${id}`, { // Adjust URL to match your controller and method
                                method: 'DELETE'
                            })
                            .then(response => response.json())
                            .then(data => {
                                // Reload locations
                                // Optionally show success message or handle other UI updates
                                location.reload(); // Refresh the page
                            })
                            .catch(error => {
                                console.error('Error deleting location:', error);
                                // Optionally show error message or handle other UI updates
                            });
                        }
                    });
                });

            })
            .catch(error => console.error('Error loading location data:', error));
    }

    // Load locations initially
    loadLocations();

    // Form submission handler for adding/editing location
    $('#locationForm').submit(function(event) {
        event.preventDefault();
        var id = $('#locationId').val();
        var formData = {
            nama: $('#nama').val(),
            latitude: $('#latitude').val(),
            longitude: $('#longitude').val(),
            keterangan: $('#keterangan').val()
        };

        var method = 'POST';
        var url = '/map/addLocation'; // Adjust URL to match your controller and method

        // If id is present, we're editing an existing location
        if (id) {
            method = 'PUT';
            url = `/map/editLocation/${id}`; // Adjust URL to match your controller and method
        }

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            // Close modal
            $('#locationModal').modal('hide');
            // Reload locations
            location.reload(); // Refresh the page
        })
        .catch(error => console.error('Error saving location:', error));
    });

    $('#tambahLocationBtn').click(function() {
        // Reset form values for adding new location
        $('#locationId').val('');
        $('#nama').val('');
        $('#latitude').val('');
        $('#longitude').val('');
        $('#keterangan').val('');
        $('#locationModalLabel').text('Tambah Lokasi');
    });
    </script>
</body>
</html>
