const map = document.getElementById('map');
const mapSidebar = document.getElementById('mapSidebar');
// TODO: See for a better solution?
if (map) {
class GPXMapManager {
    constructor() {
      this.map = null;
      this.currentGPXLayer = null;
      this.currentGPXUrl = "";
      this.lastUploadedBlobUrl = null;
      this.mapLayers = {};
      this.initializeMap();
      if (mapSidebar) {
        this.setupEventListeners();
      }
    }

    initializeMap() {
      this.map = L.map('map').setView([50.62925, 3.057256], 13);
      
      this.mapLayers = {
        osm: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 19,
          attribution: '© OpenStreetMap contributors'
        }),
        esri: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
          attribution: 'Tiles © Esri'
        }),
        terrain: L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
          maxZoom: 17,
          attribution: '© OpenTopoMap contributors'
        })
      };
      
      this.mapLayers.osm.addTo(this.map);
    }

    async addGPXTrack(url) {
      this.resetMap();

      return new Promise((resolve, reject) => {
        this.currentGPXLayer = new L.GPX(url, {
          async: true,
          marker_options: {
            startIconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
            endIconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png'
          }
        })
        .on('loaded', e => {
          this.map.fitBounds(e.target.getBounds());
          this.currentGPXUrl = url;
          if (mapSidebar) {
            this.updateTrackStats(e.target);
            document.getElementById('download-gpx').disabled = false;
          }
          resolve();
        })
        .on('error', e => {
          reject(new Error('Failed to load GPX track'));
        })
        .addTo(this.map);
      });
    }

    updateTrackStats(gpxLayer) {
      const stats = document.getElementById('stats-content');
      const statsContainer = document.getElementById('track-stats');
      
      const distance = (gpxLayer.get_distance() / 1000).toFixed(2);
      const elevation = gpxLayer.get_elevation_gain().toFixed(0);

      // Calculate the estimated travel time (16 km/h)
      const estimatedTime = this.estimateTravelTime(distance);

      stats.innerHTML = `
        <div class="grid grid-cols-2 gap-2">
          <div>Distance:</div><div>${distance} km</div>
          <div>Dénivelé:</div><div>${elevation} m</div>
        </div>
      `;
      
      // Display the estimated travel time
      const estimatedTimeElement = document.getElementById('estimated-time');
      estimatedTimeElement.textContent = `Temps de trajet estimé: ${estimatedTime}`;
      
      statsContainer.classList.remove('hidden');
    }

    estimateTravelTime(distance) {
      const averageSpeed = 16; // Average speed in km/h for a bike
      const timeInHours = distance / averageSpeed; // Time in hours
      const hours = Math.floor(timeInHours);
      const minutes = Math.round((timeInHours - hours) * 60);
      return `${hours}h ${minutes}m`;
    }

    resetMap() {
      if (this.currentGPXLayer) {
        this.map.removeLayer(this.currentGPXLayer);
        this.currentGPXLayer = null;
      }
      if (mapSidebar) {
        document.getElementById('track-stats').classList.add('hidden');
        document.getElementById('download-gpx').disabled = true;
      }
    }

    setupEventListeners() {
      // Map style changes
      document.getElementById('map-style').addEventListener('change', (e) => {
        Object.values(this.mapLayers).forEach(layer => this.map.removeLayer(layer));
        this.mapLayers[e.target.value].addTo(this.map);
      });

      // GPX list clicks
      document.querySelectorAll('#gpx-list>li').forEach(li => {
        li.addEventListener('click', () => {
          this.addGPXTrack(li.dataset.url);
        });
      });

      // Download button
      document.getElementById('download-gpx').addEventListener('click', () => {
        if (this.currentGPXUrl) {
          const link = document.createElement('a');
          link.href = this.currentGPXUrl;
          link.download = this.currentGPXUrl.split('/').pop();
          link.click();
        }
      });
    }
  }

  // Initialize the application
  document.addEventListener('DOMContentLoaded', () => {
    const app = new GPXMapManager();
    const defaultTrack = map.dataset.url;
    if (defaultTrack) app.addGPXTrack(defaultTrack);
  });
}