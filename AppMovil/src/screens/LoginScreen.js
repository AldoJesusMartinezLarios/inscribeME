import React, { useState } from 'react';
import { View, StyleSheet, Alert, TouchableOpacity, Text, Image } from 'react-native';
import InputField from '../components/InputField';
import AsyncStorage from '@react-native-async-storage/async-storage';
import logoImage from '../../assets/control-parental.png'; // Reemplaza con la ruta correcta de tu imagen

const PRIMARY_COLOR = '#8B0000'; // Vino oscuro
const SECONDARY_COLOR = '#0000FF'; // Azul
const BACKGROUND_COLOR = '#F5F5F5'; // Gris claro

const LoginScreen = ({ navigation }) => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');

  const handleLogin = async () => {
    try {
      const response = await fetch('https://fastapi-inscribeme.onrender.com/token', {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'grant_type=&username=' + encodeURIComponent(email) + '&password=' + encodeURIComponent(password) + '&scope=&client_id=&client_secret='
      });
      const data = await response.json();
      if (response.ok) {
        await AsyncStorage.setItem('email', email);
        navigation.navigate('Home');
      } else {
        Alert.alert('Error', 'Email o contraseña incorrectos');
      }
    } catch (error) {
      console.error('Error al iniciar sesión:', error);
      Alert.alert('Error', 'Se produjo un error al intentar iniciar sesión');
    }
  };

  return (
    <View style={styles.container}>
      <Image source={logoImage} style={styles.logo} />
      <View style={styles.contentContainer}>
        <Text style={styles.title}>Iniciar Sesión</Text>
        <View style={styles.inputContainer}>
          <InputField
            placeholder="Correo electrónico"
            value={email}
            onChangeText={setEmail}
            autoCapitalize="none"
            style={styles.input}
          />
          <InputField
            placeholder="Contraseña"
            value={password}
            onChangeText={setPassword}
            secureTextEntry
            style={styles.input}
          />
        </View>
        <TouchableOpacity style={styles.button} onPress={handleLogin}>
          <Text style={styles.buttonText}>Iniciar Sesión</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: BACKGROUND_COLOR,
    justifyContent: 'center',
    alignItems: 'center',
  },
  contentContainer: {
    width: '80%',
    alignItems: 'center',
  },
  logo: {
    width: 150,
    height: 150,
    marginBottom: 20,
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: PRIMARY_COLOR,
    marginBottom: 20,
    textAlign: 'center',
  },
  inputContainer: {
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
    width: '100%',
    alignItems: 'center', // Agregado para centrar los campos de entrada
  },
  input: {
    width: '100%',
    height: 50,
    borderWidth: 1,
    borderColor: PRIMARY_COLOR,
    borderRadius: 10,
    paddingHorizontal: 10,
    marginBottom: 20,
  },
  button: {
    backgroundColor: PRIMARY_COLOR,
    paddingVertical: 15,
    paddingHorizontal: 30,
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
  buttonText: {
    color: '#FFF',
    fontSize: 18,
    fontWeight: 'bold',
    textAlign: 'center',
  },
});

export default LoginScreen;