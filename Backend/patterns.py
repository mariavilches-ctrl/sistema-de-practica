# patterns.py - Implementación de Patrones de Diseño
# Coloca este archivo en: Backend/patterns.py

from abc import ABC, abstractmethod
from datetime import datetime, timedelta
from typing import List, Dict, Any

# ==========================================
# PATTERN 1: FACTORY - Creación de Tipos de Práctica
# ==========================================

class TipoPractica(ABC):
    """Clase abstracta para tipos de práctica"""
    def __init__(self, nombre: str, horas_requeridas: int):
        self.nombre = nombre
        self.horas_requeridas = horas_requeridas
    
    @abstractmethod
    def obtener_detalles(self) -> Dict:
        pass

class PracticaInicial(TipoPractica):
    """Práctica Inicial - 80 horas"""
    def obtener_detalles(self) -> Dict:
        return {
            'tipo': 'Práctica I',
            'horas': self.horas_requeridas,
            'duracion_meses': 2,
            'descripcion': 'Introducción a la profesión'
        }

class PracticaIntermedia(TipoPractica):
    """Práctica Intermedia - 160 horas"""
    def obtener_detalles(self) -> Dict:
        return {
            'tipo': 'Práctica Intermedia',
            'horas': self.horas_requeridas,
            'duracion_meses': 4,
            'descripcion': 'Aplicación de conocimientos intermedios'
        }

class PracticaProfesional(TipoPractica):
    """Práctica Profesional - 320 horas"""
    def obtener_detalles(self) -> Dict:
        return {
            'tipo': 'Práctica Profesional',
            'horas': self.horas_requeridas,
            'duracion_meses': 8,
            'descripcion': 'Experiencia profesional completa'
        }

class TipoPracticaFactory:
    """Factory para crear tipos de práctica"""
    _tipos = {
        'inicial': (PracticaInicial, 80),
        'intermedia': (PracticaIntermedia, 160),
        'profesional': (PracticaProfesional, 320)
    }
    
    @classmethod
    def crear_tipo(cls, tipo: str) -> TipoPractica:
        """Crea un tipo de práctica según el tipo especificado"""
        if tipo.lower() not in cls._tipos:
            raise ValueError(f"Tipo de práctica no válido: {tipo}")
        
        clase, horas = cls._tipos[tipo.lower()]
        return clase(tipo, horas)
    
    @classmethod
    def obtener_todos_tipos(cls) -> List[Dict]:
        """Retorna todos los tipos disponibles"""
        tipos = []
        for clave in cls._tipos:
            obj = cls.crear_tipo(clave)
            tipos.append(obj.obtener_detalles())
        return tipos

# ==========================================
# PATTERN 2: STRATEGY - Algoritmos de Calendarización
# ==========================================

class EstrategiaCalendarizacion(ABC):
    """Clase abstracta para estrategias de calendarización"""
    
    @abstractmethod
    def generar_calendario(self, horas_totales: int, fecha_inicio: datetime, 
                          sesiones_por_semana: int) -> List[Dict]:
        pass

class CalendarizacionUniforme(EstrategiaCalendarizacion):
    """Distribuye las horas uniformemente cada semana"""
    
    def generar_calendario(self, horas_totales: int, fecha_inicio: datetime, 
                          sesiones_por_semana: int) -> List[Dict]:
        sesiones = []
        horas_por_sesion = horas_totales / (sesiones_por_semana * 16)  # 4 meses
        
        for semana in range(16):
            for dia_sesion in range(sesiones_por_semana):
                fecha = fecha_inicio + timedelta(weeks=semana, days=dia_sesion)
                sesiones.append({
                    'fecha': fecha.strftime('%Y-%m-%d'),
                    'horas': int(horas_por_sesion),
                    'estado': 'Programada'
                })
        return sesiones

class CalendarizacionIntensiva(EstrategiaCalendarizacion):
    """Carga más horas al inicio, menos al final"""
    
    def generar_calendario(self, horas_totales: int, fecha_inicio: datetime, 
                          sesiones_por_semana: int) -> List[Dict]:
        sesiones = []
        total_sesiones = sesiones_por_semana * 16
        
        for semana in range(16):
            # Factor de intensidad: más horas al inicio
            factor = 2 - (semana / 16)
            horas_semana = (horas_totales / total_sesiones) * factor
            
            for dia_sesion in range(sesiones_por_semana):
                fecha = fecha_inicio + timedelta(weeks=semana, days=dia_sesion)
                sesiones.append({
                    'fecha': fecha.strftime('%Y-%m-%d'),
                    'horas': int(horas_semana / sesiones_por_semana),
                    'estado': 'Programada'
                })
        return sesiones

class CalendarizacionProgresiva(EstrategiaCalendarizacion):
    """Aumenta horas gradualmente cada semana"""
    
    def generar_calendario(self, horas_totales: int, fecha_inicio: datetime, 
                          sesiones_por_semana: int) -> List[Dict]:
        sesiones = []
        total_sesiones = sesiones_por_semana * 16
        
        for semana in range(16):
            # Factor progresivo: aumenta cada semana
            factor = (semana + 1) / 16
            horas_semana = (horas_totales / total_sesiones) * factor
            
            for dia_sesion in range(sesiones_por_semana):
                fecha = fecha_inicio + timedelta(weeks=semana, days=dia_sesion)
                sesiones.append({
                    'fecha': fecha.strftime('%Y-%m-%d'),
                    'horas': int(horas_semana / sesiones_por_semana),
                    'estado': 'Programada'
                })
        return sesiones

# ==========================================
# PATTERN 3: OBSERVER - Seguimiento de Cambios
# ==========================================

class Observador(ABC):
    """Interfaz para observadores"""
    
    @abstractmethod
    def actualizar(self, evento: str, datos: Dict):
        pass

class CalendarioObservable:
    """Clase que notifica cambios en el calendario"""
    
    def __init__(self):
        self._observadores: List[Observador] = []
    
    def agregar_observador(self, observador: Observador):
        """Agrega un observador"""
        self._observadores.append(observador)
    
    def quitar_observador(self, observador: Observador):
        """Remueve un observador"""
        self._observadores.remove(observador)
    
    def notificar_observadores(self, evento: str, datos: Dict):
        """Notifica a todos los observadores"""
        for observador in self._observadores:
            observador.actualizar(evento, datos)

class RegistroSeguimiento(Observador):
    """Observador que registra todos los cambios"""
    
    def __init__(self):
        self.registro = []
    
    def actualizar(self, evento: str, datos: Dict):
        """Registra el evento"""
        self.registro.append({
            'timestamp': datetime.now().isoformat(),
            'evento': evento,
            'datos': datos
        })
    
    def obtener_registro(self) -> List[Dict]:
        return self.registro

class NotificadorSupervisor(Observador):
    """Observador que notifica al supervisor"""
    
    def actualizar(self, evento: str, datos: Dict):
        """Notifica al supervisor de cambios importantes"""
        if evento in ['sesion_completada', 'horas_alcanzadas', 'practica_finalizada']:
            print(f"📢 Notificación al Supervisor: {evento} - {datos}")

# ==========================================
# PATTERN 4: COMPOSITE - Estructura Jerárquica
# ==========================================

class ComponenteActividad(ABC):
    """Componente abstracto para la estructura compuesta"""
    
    @abstractmethod
    def obtener_horas(self) -> int:
        pass
    
    @abstractmethod
    def obtener_descripcion(self) -> str:
        pass
    
    @abstractmethod
    def obtener_estructura(self) -> Dict:
        pass

class Actividad(ComponenteActividad):
    """Hoja: Actividad individual"""
    
    def __init__(self, id_actividad: int, nombre: str, horas: int, descripcion: str):
        self.id_actividad = id_actividad
        self.nombre = nombre
        self.horas = horas
        self.descripcion = descripcion
    
    def obtener_horas(self) -> int:
        return self.horas
    
    def obtener_descripcion(self) -> str:
        return f"{self.nombre}: {self.descripcion}"
    
    def obtener_estructura(self) -> Dict:
        return {
            'id': self.id_actividad,
            'tipo': 'Actividad',
            'nombre': self.nombre,
            'horas': self.horas,
            'descripcion': self.descripcion
        }

class Sesion(ComponenteActividad):
    """Rama: Sesión que contiene actividades"""
    
    def __init__(self, id_sesion: int, fecha: str, num_sesion: int):
        self.id_sesion = id_sesion
        self.fecha = fecha
        self.num_sesion = num_sesion
        self.actividades: List[ComponenteActividad] = []
    
    def agregar_actividad(self, componente: ComponenteActividad):
        """Agrega una actividad a la sesión"""
        self.actividades.append(componente)
    
    def remover_actividad(self, componente: ComponenteActividad):
        """Remueve una actividad de la sesión"""
        self.actividades.remove(componente)
    
    def obtener_horas(self) -> int:
        """Suma las horas de todas las actividades"""
        return sum(act.obtener_horas() for act in self.actividades)
    
    def obtener_descripcion(self) -> str:
        return f"Sesión #{self.num_sesion} - {self.fecha}"
    
    def obtener_estructura(self) -> Dict:
        return {
            'id': self.id_sesion,
            'tipo': 'Sesion',
            'fecha': self.fecha,
            'num_sesion': self.num_sesion,
            'horas_totales': self.obtener_horas(),
            'actividades': [act.obtener_estructura() for act in self.actividades]
        }

class PracticaComposite(ComponenteActividad):
    """Rama raíz: Práctica que contiene sesiones"""
    
    def __init__(self, id_practica: int, tipo_practica: str, fecha_inicio: str):
        self.id_practica = id_practica
        self.tipo_practica = tipo_practica
        self.fecha_inicio = fecha_inicio
        self.sesiones: List[ComponenteActividad] = []
    
    def agregar_sesion(self, sesion: Sesion):
        """Agrega una sesión a la práctica"""
        self.sesiones.append(sesion)
    
    def remover_sesion(self, sesion: Sesion):
        """Remueve una sesión de la práctica"""
        self.sesiones.remove(sesion)
    
    def obtener_horas(self) -> int:
        """Suma las horas de todas las sesiones"""
        return sum(ses.obtener_horas() for ses in self.sesiones)
    
    def obtener_descripcion(self) -> str:
        return f"Práctica de {self.tipo_practica} iniciada {self.fecha_inicio}"
    
    def obtener_estructura(self) -> Dict:
        return {
            'id': self.id_practica,
            'tipo': 'Practica',
            'tipo_practica': self.tipo_practica,
            'fecha_inicio': self.fecha_inicio,
            'horas_totales': self.obtener_horas(),
            'num_sesiones': len(self.sesiones),
            'sesiones': [ses.obtener_estructura() for ses in self.sesiones]
        }