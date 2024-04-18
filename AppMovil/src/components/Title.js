import React from 'react';
import { Text, StyleSheet } from 'react-native';

const Title = ({ text }) => {
  return <Text style={styles.title}>{text}</Text>;
};

const styles = StyleSheet.create({
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 20,
  },
});

export default Title;