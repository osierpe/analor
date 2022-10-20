import dados from './dadosForm';
import FormAnalise from './FormAnalise';

const analiseEl = dados.map(dado => {
  return <FormAnalise item={dado} key={dado.nome} />;
});

export default function Form() {
  return (
    <form>
      <h1>Analor</h1>
      {analiseEl}
    </form>
  );
}
