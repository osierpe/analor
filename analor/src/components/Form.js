import dados from './dadosForm';
import FormAnalise from './FormAnalise';
import Buttons from './Buttons';

const analiseEl = dados.map(dado => {
  return <FormAnalise item={dado} key={dado.nome} />;
});

export default function Form() {
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
