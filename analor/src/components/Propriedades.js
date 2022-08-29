import Propriedades_form from './Propriedades_form';
import f from '../functions.js';

const caracteristicas = [
  'Peso Molecular',
  'Ponto de Fusão',
  'Ponto de Ebulição',
];
export default function Propriedades() {
  const propriedades_form = caracteristicas.map(car => {
    return (
      <Propriedades_form
        caracteristica={car}
        iniciais={f.pegarIniciais(car)}
        key={f.pegarIniciais(car)}
      />
    );
  });
  return (
    <div className='propriedades'>
      <h2>Propriedades</h2>
      {propriedades_form}
    </div>
  );
}
