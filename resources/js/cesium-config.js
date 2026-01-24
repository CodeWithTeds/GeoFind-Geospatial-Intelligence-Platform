
// Configure Cesium to use the backend proxy for Ion requests
if (window.Cesium) {
    // Redirect Cesium Ion requests to our secure backend proxy
    Cesium.Ion.defaultServer = window.location.origin + '/api/cesium/';
    
    // Set a placeholder token to satisfy Cesium's client-side validation checks
    // The REAL token is injected securely by the backend proxy (CesiumProxyController)
    Cesium.Ion.defaultAccessToken = 'token-secured-by-backend-proxy';
}
