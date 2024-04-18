import React from 'react';
import { TextInput, StyleSheet } from 'react-native';

const InputField = ({ placeholder, value, onChangeText, secureTextEntry = false, autoCapitalize = 'sentences' }) => {
  return (
    <TextInput
      style={styles.input}
      placeholder={placeholder}
      value={value}
      onChangeText={onChangeText}
      secureTextEntry={secureTextEntry}
      autoCapitalize={autoCapitalize}
    />
  );
};

const styles = StyleSheet.create({
  input: {
    width: '80%',
    height: 40,
    borderColor: 'gray',
    borderWidth: 1,
    marginVertical: 10,
    paddingHorizontal: 10,
  },
});

export default InputField;