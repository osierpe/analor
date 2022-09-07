import Logo from './components/Logo.js';
import Elementos from './components/Elementos.js';
import Propriedades from './components/Propriedades.js';
import GrupoFuncEsqCar from './components/GrupoFuncEsqCar.js';
import Cas from './components/Cas.js';
export default function App() {
  return (
    <form method='post'>
      <Logo />
      <Elementos />
      <Propriedades />
      <GrupoFuncEsqCar />
      <Cas />
    </form>
  );
}
