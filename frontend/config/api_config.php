<?php
class ApiClient {
    private $baseUrl;
    private $token;
    
    public function __construct() {
        $this->baseUrl = 'http://localhost:5000/api';
        $this->token = $_SESSION['jwt_token'] ?? null;
    }
    
    private function makeRequest($endpoint, $method = 'GET', $data = null) {
        $url = $this->baseUrl . $endpoint;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
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
        curl_close($ch);
        
        if ($httpCode >= 400) {
            return ['error' => true, 'message' => 'Error en la petición', 'code' => $httpCode];
        }
        
        return json_decode($response, true);
    }
    
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