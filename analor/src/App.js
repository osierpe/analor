/* eslint-disable no-eval */
import { useState } from 'react';
import dados from './components/dadosForm';
import FormAnalise from './components/FormAnalise';
import Buttons from './components/Buttons';
import './sass/styles.css';

export default function App() {
  const [formData, setFormData] = useState({
    //Elementos
    temCarbono: '',
    quantCarbono: '',
    temOxigênio: '',
    quantOxigênio: '',
    temHidrogênio: '',
    quantHidrogênio: '',
    temNitrogênio: '',
    quantNitrogênio: '',
    temEnxofre: '',
    quantEnxofre: '',
    temCloro: '',
    quantCloro: '',
    temBromo: '',
    quantBromo: '',
    temIodo: '',
    quantIodo: '',
    temFlúor: '',
    quantFlúor: '',

    // Propriedades

    pesoMolecularMin: '',
    pesoMolecularMax: '',
    pontoFusãoMin: '',
    pontoFusãoMax: '',
    pontoEbuliçãoMin: '',
    pontoEbuliçãoMax: '',

    // ECGF
    gFunc1: '',
    inex1: 'incluir',
    gFunc2: '',
    inex2: 'incluir',
    gFunc3: '',
    inex3: 'incluir',
    gFunc4: '',
    inex4: 'incluir',
    gFunc5: '',
    inex5: 'incluir',
    gFunc6: '',
    inex6: 'incluir',

    //cas
    cas: '',
  });

  const handleChange = function (event) {
    const { name, value, type, checked } = event.target;

    if (name.slice(0, 5) === 'quant') {
      const temElemento = `tem${name.slice(5)}`;

      setFormData(prevData => ({
        ...prevData,
        [name]: value,
        [temElemento]: value > 0,
      }));
    } else if (name.slice(0, 3) === 'tem') {
      const quantElemento = `quant${name.slice(3)}`;
      setFormData(prevData => ({
        ...prevData,
        [name]: checked ? checked : '',
        [quantElemento]: !checked ? '' : '',
      }));
      //
      // Código velho para manter o max sempre acima do min, que não estava funcionando

      // } else if (name.slice(-3) === 'Max') {
      //   const valorMin = name.replace(/Max$/, 'Min');
      //   setFormData(prevData => ({
      //     ...prevData,
      //     [name]:
      //       value > eval(`formData.${valorMin}`)
      //         ? value
      //         : eval(`formData.${valorMin}`),
      //   }));
      //
    } else if (name.slice(-3) === 'Min') {
      const valorMax = name.replace(/Min$/, 'Max');
      setFormData(prevData => ({
        ...prevData,
        [name]: value,
        [valorMax]:
          value > eval(`formData.${valorMax}`)
            ? value
            : eval(`formData.${valorMax}`),
      }));
    } else {
      setFormData(prevData => ({
        ...prevData,
        [name]: type === 'checkbox' ? checked : value,
      }));
    }
  };

  const analiseEl = dados.map(dado => {
    return (
      <FormAnalise
        item={dado}
        key={dado.nome}
        formData={formData}
        handleChange={handleChange}
      />
    );
  });

  const handleSubmit = event => {
    event.preventDefault();

    fetch('http://localhost:5000', {
      method: 'POST',
      body: formData
    })
  };

  return (
    <>
      <header>
        <h1 className='logo'>Analor</h1>
      </header>
      <main>
        <form onSubmit={handleSubmit}>
          <div className='analise'>{analiseEl}</div>
          <Buttons />
        </form>
      </main>
    </>
  );
}
