import 'react-native-gesture-handler';
import React from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import LoginScreen from './src/screens/LoginScreen';
import HomeScreen from './src/screens/HomeScreen';
import Historial from './src/screens/Historial'

const Stack = createStackNavigator();

const App = () => {
  return (
    <NavigationContainer>
      <Stack.Navigator initialRouteName="Login">
        <Stack.Screen name="Login" component={LoginScreen} options={{ title: 'Iniciar Sesión' }} />
        <Stack.Screen name="Home" component={HomeScreen} options={{ title: 'Inicio' }} />
        <Stack.Screen name="Historial" component={Historial} options={{ title: 'Historial'}} />
      </Stack.Navigator>
    </NavigationContainer>
  );
};

export default App;
