import Elementos_form from './Elementos_form';

const elementos = [
  'Carbono',
  'Oxigênio',
  'Hidrogênio',
  'Nitrogênio',
  'Enxofre',
  'Cloro',
  'Bromo',
  'Iodo',
  'Flour',
];

export default function Elementos() {
  const elementosForms = elementos.map(el => {
    return <Elementos_form elemento={el} key={el.slice(0, 2).toLowerCase()} />;
  });
  return (
    <div className='elementos'>
      <h2>Elementos</h2>
      {elementosForms}
      <div className='tooltip'>
        Modo de uso para busca por elementos
        <span className='tooltiptext'>
          Marque a caixa "possui" dos elementos que tenha certeza de sua
          presença e/ou sua quantidade se souber. Caso não saiba se há ou não
          presença, deixe os campos deste elemento em branco. Caso tenha certeza
          de que não há presença, digite sua quantidade = 0, e deixe a caixa
          "possui" desmarcada.
        </span>
      </div>
    </div>
  );
}
