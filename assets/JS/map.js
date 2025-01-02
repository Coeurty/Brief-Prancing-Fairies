// TODO: See for a better solution?
if (document.getElementById('map')) {
class GPXMapManager {
    constructor() {
      this.map = null;
      this.currentGPXLayer = null;
      this.currentGPXUrl = "";
      this.lastUploadedBlobUrl = null;
      this.mapLayers = {};
      this.initializeMap();
      this.setupEventListeners();
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

    async loadGPXFile(file) {
      if (!this.validateGPXFile(file)) return;

      this.showLoading();

      try {
        const gpxData = await this.readFileAsync(file);
        if (this.lastUploadedBlobUrl) {
          URL.revokeObjectURL(this.lastUploadedBlobUrl);
        }

        this.lastUploadedBlobUrl = URL.createObjectURL(new Blob([gpxData]));
        await this.addGPXTrack(this.lastUploadedBlobUrl);

        document.getElementById('file-name').textContent = file.name;
      } catch (error) {
        this.showError('Failed to load GPX file: ' + error.message);
      } finally {
        this.hideLoading();
      }
    }

    validateGPXFile(file) {
      const MAX_SIZE = 5 * 1024 * 1024; // 5MB

      if (!file.name.endsWith('.gpx')) {
        this.showError('Please upload a valid GPX file');
        return false;
      }

      if (file.size > MAX_SIZE) {
        this.showError('File size exceeds 5MB limit');
        return false;
      }

      return true;
    }

    readFileAsync(file) {
      return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = e => resolve(e.target.result);
        reader.onerror = e => reject(new Error('File reading failed'));
        reader.readAsText(file);
      });
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
          this.updateTrackStats(e.target);
          this.currentGPXUrl = url;
          document.getElementById('download-gpx').disabled = false;
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
          <div>Elevation Gain:</div><div>${elevation} m</div>
        </div>
      `;
      
      // Display the estimated travel time
      const estimatedTimeElement = document.getElementById('estimated-time');
      estimatedTimeElement.textContent = `Temps de trajet estimé: ${estimatedTime}`;
      
      statsContainer.classList.remove('hidden');
    }

    formatDuration(seconds) {
      const hours = Math.floor(seconds / 3600);
      const minutes = Math.floor((seconds % 3600) / 60);
      return `${hours}h ${minutes}m`;
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
      document.getElementById('track-stats').classList.add('hidden');
      document.getElementById('download-gpx').disabled = true;
      document.getElementById('file-name').textContent = '';
    }

    showLoading() {
      document.getElementById('loading').classList.remove('hidden');
    }

    hideLoading() {
      document.getElementById('loading').classList.add('hidden');
    }

    showError(message) {
      alert(message); // Could be replaced with a nicer UI notification
    }

    setupEventListeners() {
      // Map style changes
      document.getElementById('map-style').addEventListener('change', (e) => {
        Object.values(this.mapLayers).forEach(layer => this.map.removeLayer(layer));
        this.mapLayers[e.target.value].addTo(this.map);
      });

      // GPX file upload
      document.getElementById('gpx-upload').addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) this.loadGPXFile(file);
      });

      // GPX list clicks
      document.getElementById('gpx-list').addEventListener('click', (e) => {
        if (e.target.classList.contains('track-item')) {
          this.addGPXTrack(e.target.getAttribute('data-url'));
        }
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
    // Load default track
    app.addGPXTrack("/gpx/EuroVelo 5 Via_Romea.gpx");
  });
}