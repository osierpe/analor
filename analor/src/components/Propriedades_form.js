export default function Propriedades_form(props) {
  return (
    <div className='propriedades__form'>
      <label htmlFor='scales'>{props.caracteristica}</label>
      <input
        type='number'
        step='0.01'
        min='0'
        name={props.iniciais + '1'}
      />à <input type='number' min='0' step='0.01' name={props.iniciais + 2} />
    </div>
  );
}
