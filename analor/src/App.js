import { useState } from 'react';
import dados from './components/dadosForm';
import FormAnalise from './components/FormAnalise';
import Buttons from './components/Buttons';
import './sass/styles.css';

export default function App() {
  const [formData, setFormData] = useState({
    //Elementos
    temCarbono: undefined,
    quantCarbono: undefined,
    temOxigênio: undefined,
    quantOxigênio: undefined,
    temHidrogênio: undefined,
    quantHidrogênio: undefined,
    temNitrogênio: undefined,
    quantNitrogênio: undefined,
    temEnxofre: undefined,
    quantEnxofre: undefined,
    temCloro: undefined,
    quantCloro: undefined,
    temBromo: undefined,
    quantBromo: undefined,
    temIodo: undefined,
    quantIodo: undefined,
    temFlúor: undefined,
    quantFlúor: undefined,

    // Propriedades

    pesoMolecularMin: undefined,
    pesoMolecularMax: undefined,
    pontoFusãoMin: undefined,
    pontoFusãoMax: undefined,
    pontoEbuliçãoMin: undefined,
    pontoEbuliçãoMax: undefined,

    // ECGF
    gfunc1: '',
    inex1: 'incluir',
    gfunc2: '',
    inex2: 'incluir',
    gfunc3: '',
    inex3: 'incluir',
    gfunc4: '',
    inex4: 'incluir',
    gfunc5: '',
    inex5: 'incluir',
    gfunc6: '',
    inex6: 'incluir',

    //cas
    cas: '',
  });

  const handleChange = function (event) {
    const { name, value } = event.target;
    setFormData(prevData => ({
      ...prevData,
      [name]: value,
    }));
  };

  const analiseEl = dados.map(dado => {
    return (
      <FormAnalise
        item={dado}
        key={dado.nome}
        formData={formData}
        setFormData={setFormData}
      />
    );
  });

  return (
    <>
      <header>
        <h1 className='Logo'>Analor</h1>
      </header>
      <main>
        <form>
          <div className='analise'>{analiseEl}</div>
          <Buttons />
        </form>
      </main>
    </>
  );
}
