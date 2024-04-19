import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, FlatList } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';

const PRIMARY_COLOR = '#8B0000'; // Vino oscuro
const SECONDARY_COLOR = '#0000FF'; // Azul
const BACKGROUND_COLOR = '#F5F5F5'; // Gris claro

const Historial = () => {
  const [historial, setHistorial] = useState([]);
  const [mensaje, setMensaje] = useState('');

  useEffect(() => {
    obtenerHistorial();
  }, []);

  const obtenerHistorial = async () => {
    try {
      const email = await AsyncStorage.getItem('email');

      const response = await fetch(`https://fastapi-mongodb-b1r3.onrender.com/entradas_salidas/${email}`);
      const data = await response.json();

      if (response.ok) {
        // Combinar entradas y salidas en una sola lista y ordenar por momento
        const historialCombinado = [...data.entradas, ...data.salidas];
        historialCombinado.sort((a, b) => new Date(a.momento) - new Date(b.momento));

        // Formatear la hora de entrada/salida antes de establecer el estado
        const historialFormateado = historialCombinado.map(item => ({
          ...item,
          momento: new Date(item.momento).toLocaleString('es-ES', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
          }),
          tipo: item.lector === 'entrada' ? 'Entrada' : 'Salida', // Agregar el tipo de registro
        }));

        setHistorial(historialFormateado);
        if (historialFormateado.length === 0) {
          setMensaje('No hay entradas ni salidas registradas.');
        } else {
          setMensaje('');
        }
      } else {
        console.error('Error al obtener historial:', data);
      }
    } catch (error) {
      console.error('Error al obtener historial:', error);
    }
  };
  
  return (
    <View style={styles.container}>
      {/* Título */}
      <Text style={styles.title}>Historial de Entradas y Salidas</Text>

      <Text style={styles.subtitle}>Solo se puede visualizar 7 dias antes de la fecha actual</Text>


      {/* Historial */}
      {mensaje ? (
        <Text style={styles.mensaje}>{mensaje}</Text>
      ) : (
        <FlatList
          data={historial}
          keyExtractor={(item) => item.momento} // Usar momento como clave única
          renderItem={({ item }) => (
            <View style={styles.itemContainer}>
              <Text style={styles.itemMomento}>{item.momento}</Text>
              <Text style={[styles.itemTipo, item.tipo === 'Entrada' ? styles.entrada : styles.salida]}>{item.tipo}</Text>
            </View>
          )}
        />
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: BACKGROUND_COLOR,
    padding: 20,
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: PRIMARY_COLOR,
    marginBottom: 20,
    textAlign: 'center',
  },
  subtitle: {
    fontSize: 14,
    color: PRIMARY_COLOR,
    marginBottom: 20,
    textAlign: 'center',
  },
  itemContainer: {
    marginBottom: 10,
    padding: 10,
    backgroundColor: '#FFF',
    borderRadius: 5,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  itemMomento: {
    fontSize: 16,
    color: PRIMARY_COLOR,
  },
  itemTipo: {
    fontSize: 16,
    fontWeight: 'bold',
    marginLeft: 10,
    padding: 5,
    borderRadius: 5,
  },
  entrada: {
    color: '#00FF00', // Color verde para las entradas
    backgroundColor: '#C0FFC0', // Fondo verde claro
  },
  salida: {
    color: '#FF0000', // Color rojo para las salidas
    backgroundColor: '#FFC0C0', // Fondo rojo claro
  },
  mensaje: {
    fontSize: 16,
    color: SECONDARY_COLOR,
    textAlign: 'center',
  },
});

export default Historial;
