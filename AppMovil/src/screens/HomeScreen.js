import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, FlatList, TouchableOpacity } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useNavigation } from '@react-navigation/native'; // Importar hook de navegación
import Historial from './Historial'; // Importar componente Historial desde el archivo Historial.js

const PRIMARY_COLOR = '#8B0000'; // Vino oscuro
const SECONDARY_COLOR = '#0000FF'; // Azul
const BACKGROUND_COLOR = '#F5F5F5'; // Gris claro

const HomeScreen = () => {
  const navigation = useNavigation(); // Hook de navegación

  const [entradas, setEntradas] = useState([]);
  const [salidas, setSalidas] = useState([]);
  const [mensajeEntradas, setMensajeEntradas] = useState('');
  const [mensajeSalidas, setMensajeSalidas] = useState('');
  const [currentDate, setCurrentDate] = useState('');

  useEffect(() => {
    obtenerEntradas();
    obtenerSalidas();
    obtenerFechaActual();
  }, []);

  const obtenerFechaActual = () => {
    const date = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const formattedDate = new Intl.DateTimeFormat('es-ES', options).format(date);
    setCurrentDate(formattedDate);
  };

  const obtenerEntradas = async () => {
    try {
      const email = await AsyncStorage.getItem('email');

      const response = await fetch(`https://fastapi-mongodb-b1r3.onrender.com/entradas_hoy/${email}`);
      const data = await response.json();

      if (response.ok) {
        // Formatear la hora de entrada antes de establecer el estado
        const entradasFormateadas = data.map(item => ({
          ...item,
          momento: new Date(item.momento).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        }));
        setEntradas(entradasFormateadas);
        if (entradasFormateadas.length === 0) {
          setMensajeEntradas('No hay entradas registradas hoy.');
        } else {
          setMensajeEntradas('');
        }
      } else {
        const responseBody = await response.json();
        if (response.status === 404 && responseBody.detail) {
          setMensajeEntradas(responseBody.detail); // Mostrar el mensaje específico del error 404
        } else {
          console.error('Error al obtener entradas:', responseBody);
          setMensajeEntradas('Error al obtener entradas. Por favor, inténtalo de nuevo.'); // Mensaje genérico de error
        }
      }
    } catch (error) {
      console.error('Error al obtener entradas:', error);
      setMensajeEntradas('El alumno no ha entrado a la institución.'); // Mensaje genérico de error
    }
  };


  const obtenerSalidas = async () => {
    try {
      const email = await AsyncStorage.getItem('email');

      const response = await fetch(`https://fastapi-mongodb-b1r3.onrender.com/salidas_hoy/${email}`);
      const data = await response.json();

      if (response.ok) {
        // Formatear la hora de salida antes de establecer el estado
        const salidasFormateadas = data.map(item => ({
          ...item,
          momento: new Date(item.momento).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        }));
        setSalidas(salidasFormateadas);
        if (salidasFormateadas.length === 0) {
          setMensajeSalidas('No hay salidas registradas hoy.');
        } else {
          setMensajeSalidas('');
        }
      } else {
        const responseBody = await response.json();
        if (response.status === 404 && responseBody.detail) {
          setMensajeSalidas(responseBody.detail); // Mostrar el mensaje específico del error 404
        } else {
          console.error('Error al obtener salidas:', responseBody);
          setMensajeSalidas('Error al obtener salidas. Por favor, inténtalo de nuevo.'); // Mensaje genérico de error
        }
      }
    } catch (error) {
      console.error('Error al obtener salidas:', error);
      setMensajeSalidas('El alumno no ha salido de la institución.'); // Mensaje genérico de error
    }
  };



  const irAHistorial = () => {
    navigation.navigate('Historial'); // Navegar a la pantalla de historial
  };

  return (
    <View style={styles.container}>
      {/* Menú */}
      <View style={styles.menuContainer}>
        <TouchableOpacity onPress={irAHistorial}>
          <Text style={styles.menuItem}>Historial</Text>
        </TouchableOpacity>
        {/* Puedes agregar más opciones de menú aquí */}
      </View>

      {/* Título */}
      <Text style={styles.title}>Control Parental</Text>

      {/* Día Actual */}
      <Text style={styles.currentDate}>{currentDate}</Text>

      <View style={styles.sectionContainer}>
        <Text style={styles.subtitle}>ENTRADA</Text>
        {mensajeEntradas ? (
          <View style={[styles.errorContainer, { backgroundColor: PRIMARY_COLOR }]}>
            <Text style={styles.errorText}>{mensajeEntradas}</Text>
          </View>
        ) : (
          <FlatList
            data={entradas}
            keyExtractor={(item) => item._id}
            renderItem={({ item }) => (
              <View style={styles.itemContainer}>
                <Text style={styles.itemText}>Hora de entrada: {item.momento}</Text>
              </View>
            )}
          />
        )}
      </View>

      <View style={styles.sectionContainer}>
        <Text style={styles.subtitle}>SALIDA</Text>
        {mensajeSalidas ? (
          <View style={[styles.errorContainer, { backgroundColor: PRIMARY_COLOR }]}>
            <Text style={styles.errorText}>{mensajeSalidas}</Text>
          </View>
        ) : (
          <FlatList
            data={salidas}
            keyExtractor={(item) => item._id}
            renderItem={({ item }) => (
              <View style={styles.itemContainer}>
                <Text style={styles.itemText}>Hora de salida: {item.momento}</Text>
              </View>
            )}
          />
        )}
      </View>
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
  menuContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 10,
  },
  menuItem: {
    fontSize: 18,
    fontWeight: 'bold',
    color: PRIMARY_COLOR,
  },
  currentDate: {
    textAlign: 'center',
    fontSize: 16,
    color: PRIMARY_COLOR,
    marginBottom: 10,
  },
  sectionContainer: {
    marginBottom: 20,
    padding: 20,
    backgroundColor: '#FFF',
    borderRadius: 10,
    shadowColor: '#000',
    shadowOffset: {
      width: 0,
      height: 2,
    },
    shadowOpacity: 0.25,
    shadowRadius: 3.84,
    elevation: 5,
  },
  subtitle: {
    textAlign: "center",
    fontSize: 20,
    fontWeight: 'bold',
    color: PRIMARY_COLOR,
    marginBottom: 10,
  },
  itemContainer: {
    marginBottom: 10,
    padding: 10,
    backgroundColor: BACKGROUND_COLOR,
    borderRadius: 5,
  },
  itemText: {
    fontSize: 16,
    color: PRIMARY_COLOR,
    textAlign: "center"
  },
  errorContainer: {
    padding: 10,
    borderRadius: 5,
    marginBottom: 10,
  },
  errorText: {
    color: '#FFF', // Color de texto blanco para que resalte
    fontSize: 16,
    textAlign: 'center',
  },
});

export default HomeScreen;
