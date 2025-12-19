import { useState, useEffect } from 'react';

function getValueFromSessionStorage(key, initialValue) {
  const savedValue = sessionStorage.getItem(key);
  if (savedValue) {
    try {
      return JSON.parse(savedValue);
    } catch (error) {
      console.error('Error parsing JSON from sessionStorage', error);
      return initialValue;
    }
  }
  return initialValue;
}

export const useSessionStorage = (key, initialValue) => {
  const [value, setValue] = useState(() => {
    return getValueFromSessionStorage(key, initialValue);
  });

  useEffect(() => {
    try {
      sessionStorage.setItem(key, JSON.stringify(value));
    } catch (error) {
      console.error('Error setting item to sessionStorage', error);
    }
  }, [key, value]);

  return [value, setValue];
};
