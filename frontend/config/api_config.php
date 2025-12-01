<?php
// Configuración de la API del backend
define('API_BASE_URL', 'http://localhost:5000'); 
define('API_TIMEOUT', 30); 

class ApiClient {
    private $baseUrl;
    private $token;
    
    public function __construct() {
        $this->baseUrl = API_BASE_URL;
        
        // Verificar si la sesión ya está activa antes de iniciarla
        if (session_status() === PHP_SESSION_NONE) {
            session_start(); 
        }
        $this->token = $_SESSION['jwt_token'] ?? null;
    }


    public function makeRequest($endpoint, $method = 'GET', $data = null) {
        $url = $this->baseUrl . $endpoint;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_TIMEOUT, API_TIMEOUT);
        
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        
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
            return ['error' => true, 'message' => 'Error de conexión con Backend: ' . $curlError];
        }
        
        // Manejo de errores HTTP 
        if ($httpCode >= 400) {
            $errorData = json_decode($response, true);
            return [
                'error' => true,
                'message' => $errorData['message'] ?? 'Error en la petición (Código: ' . $httpCode . ')',
                'code' => $httpCode
            ];
        }
        
        return json_decode($response, true) ?? [];
    }
    
   

    public function login($correo, $password) {
        return $this->makeRequest('/login', 'POST', [
            'usuario' => $correo,
            'password' => $password
        ]);
    }
    
    public function getPracticas() {
        return $this->makeRequest('/practicas');
    }
    
    public function createPractica($data) {
        return $this->makeRequest('/practicas', 'POST', $data);
    }

    public function getSesiones() {
        return $this->makeRequest('/sesiones');
    }

    public function getTipos() {
        return $this->makeRequest('/tipos');
    }

    public function getBitacora() {
        return $this->makeRequest('/bitacora');
    }

    
    public function getEstudiantes() {
        return $this->makeRequest('/estudiantes');
    }

    public function getCentros() {
        return $this->makeRequest('/centros');
    }
}
?>