export default function Buttons(props) {
  return (
    <div className='btns'>
      <a href='/' className='button'>
        Referencia Grup Func & Esq Carbono
      </a>
      <button className='button' id='submit' onClick={props.handleSubmit}>
        Aplicar filtro
      </button>
    </div>
  );
}
