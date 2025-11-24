<?php
// Configuración de la API del backend
define('API_BASE_URL', 'http://localhost:5000/api');
define('API_TIMEOUT', 30); // segundos

// Clase helper para consumir la API
class ApiClient {
    private $baseUrl;
    private $token;
    
    public function __construct() {
        $this->baseUrl = API_BASE_URL;
        $this->token = $_SESSION['jwt_token'] ?? null;
    }
    
    /**
     * Realiza una petición HTTP a la API
     * @param string $endpoint Ruta del endpoint (ej: '/practicas')
     * @param string $method Método HTTP (GET, POST, PUT, DELETE)
     * @param array|null $data Datos a enviar (se convierten a JSON)
     * @return array Respuesta de la API o array con error
     */
    public function makeRequest($endpoint, $method = 'GET', $data = null) {
        $url = $this->baseUrl . $endpoint;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_TIMEOUT, API_TIMEOUT);
        
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        
        if ($this->token) {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            return ['error' => true, 'message' => 'Error de conexión: ' . $curlError];
        }
        
        if ($httpCode >= 400) {
            $errorData = json_decode($response, true);
            return [
                'error' => true,
                'message' => $errorData['message'] ?? 'Error en la petición',
                'code' => $httpCode
            ];
        }
        
        return json_decode($response, true) ?? [];
    }
    
    // Métodos específicos (se completarán cuando el backend esté listo)
    
    public function login($correo, $password) {
        return $this->makeRequest('/login', 'POST', [
            'correo' => $correo,
            'password' => $password
        ]);
    }
    
    public function getDashboardStats() {
        return $this->makeRequest('/dashboard/stats');
    }
    
    public function getPracticas() {
        return $this->makeRequest('/practicas');
    }
    
    public function createPractica($data) {
        return $this->makeRequest('/practicas', 'POST', $data);
    }
    
    public function getBitacora() {
        return $this->makeRequest('/bitacora');
    }
    
    public function createBitacora($data) {
        return $this->makeRequest('/bitacora', 'POST', $data);
    }
}
?>