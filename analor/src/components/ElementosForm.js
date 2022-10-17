export default function ElementosForm(props) {
  return (
    <div className='elementos__form'>
      <label htmlFor='scales'>Possui {props.elemento}?</label>
      <input
        type='checkbox'
        id={'p' + props.elemento.slice(0, 2).toLowerCase()}
        name={'p' + props.elemento.slice(0, 2).toLowerCase()}
      />
      <label htmlFor='scales'>Quantidade de {props.elemento}:</label>
      <input type='number' name={props.elemento} min='0' />
    </div>
  );
}
