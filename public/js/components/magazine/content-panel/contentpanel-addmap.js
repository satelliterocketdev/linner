Vue.component ('contentpanel-addmap', {
    template: 
    `<div>
        <div class="row justify-content-center" style="font-size: 18px">
        <b><input class="borderless-input form-control" type="text" placeholder="Title" v-model="map.title"></b>
        </div>
        <div class="row justify-content-center p-2">
            <input type="text" class="form-control p-2" id="address" style="width: 50%" autocomplete="off" v-model="map.address">
        </div>
        <div class="row justify-content-center">
        <button type="button" class="btn rounded-green m-1" @click="searchAndPin">Search and Pin to Address</button>
        </div>
        <div class="row justify-content-center m-2">
            <div id="map" style="height: 400px; width: 100%"></div>
        </div>
    </div>`,
    props: ['data', 'type'],
    data() {
        return {
            marker: {},
            gmap: {},
            searchBox: {},
            content: {
                content_type: 'location',
            },
            map: {
                lat: 59.327,
                lng: 18.067,
            },
        };
    },
    mounted() {
        if (this.type == 'Edit') {
            this.map = this.data.content_message
            if (this.isValidJSON(this.data.content_message)) {
                this.map = $.parseJSON(this.data.content_message)
            }
        }

        this.content.content_message = this.map
        Object.assign(this.data, this.content)

        this.setup()
    },
    methods: {
        isValidJSON(str) {
            try {
                $.parseJSON(str)
            } catch (e) {
                return false
            }
            return true
        },
        getLatLng() {
            return {
                'lat': this.map.lat,
                'lng': this.map.lng,
            }
        },
        setup() {
            t = this
            // load map
            t.gmap = new google.maps.Map(document.getElementById('map'), {
                zoom: 13,
                center: t.getLatLng(),
            });
            // load marker
            t.marker = new google.maps.Marker({
                map: t.gmap,
                draggable: true,
                position: t.getLatLng()
            });
            t.map.lat = t.getLatLng().lat
            t.map.lng = t.getLatLng().lng

            // searchbox
            t.searchBox = new google.maps.places.SearchBox(document.getElementById('address'));
            t.gmap.addListener('bounds_changed', function() {
                // t.map.address
                t.searchBox.setBounds(t.gmap.getBounds());
            });
            google.maps.event.addListener(t.searchBox, 'places_changed', (e) => {
                // 
            })
            google.maps.event.addListener(t.marker, 'position_changed', (e) => {
                t.map.lat = t.marker.getPosition().lat()
                t.map.lng = t.marker.getPosition().lng()
            })
        },
        searchAndPin() {
            t = this
            var places = t.searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }

            var bounds = new google.maps.LatLngBounds();
            places.forEach(function(place) {
                if (!place.geometry) {
                    console.log("Returned place contains no geometry");
                    return;
                }

                t.marker.setMap(null)

                t.marker = new google.maps.Marker({
                    map: t.gmap,
                    draggable: true,
                    title: place.name,
                    position: place.geometry.location
                });

                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            })
            t.gmap.fitBounds(bounds);

            t.searchBox.setBounds(t.gmap.getBounds());
        }
    }
});