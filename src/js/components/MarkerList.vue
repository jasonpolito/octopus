<template>
    <gmap-map :center="center" :zoom="7" style="width: 100%; height: 500px">
      <gmap-cluster>
        <gmap-marker v-for="(m, index) in markers"
          :position="m.position"
          :clickable="true" :draggable="true"
          @click="center=m.position"
          :key="index"
          ></gmap-marker>
      </gmap-cluster>
    </gmap-map>
</template>
<script>
    export default {
        data () {
            return {
                center: {
                    lat: 47.376332,
                    lng: 8.547511
                },
                markers: [{
                    position: {
                        lat: 47.376332,
                        lng: 8.547511
                    },
                    infoText: 'Marker 1'
                }, {
                    position: {
                        lat: 47.374592,
                        lng: 8.548867
                    },
                    infoText: 'Marker 2'
                }, {
                    position: {
                        lat: 47.379592,
                        lng: 8.549867
                    },
                    infoText: 'Marker 3'
                }]
            }
        },
        methods: {
            toggleInfoWindow: function(marker, idx) {
                this.infoWindowPos = marker.position;
                this.infoContent = marker.infoText;
                if (this.currentMidx == idx) {
                    this.infoWinOpen = !this.infoWinOpen;
                }
                else {
                    this.infoWinOpen = true;
                    this.currentMidx = idx;
                }
            }
        }
    }
</script>