import { useState } from 'react'
import reactLogo from './assets/react.svg'
import viteLogo from '/vite.svg'
import './App.css'

function App() {
  return (
    <div style={{ textAlign: 'center', marginTop: '50px' }}>
      <h1>Frontend React funcionando!</h1>
      <p>Conectado al backend Django.</p>
      <a href="http://localhost:8000" target="_blank" rel="noopener noreferrer">
        Ver backend Django
      </a>
    </div>
  );
}

export default App
