export default function GrupoFuncEsqCarForm(props) {
  return (
    <div className='gpFunc__form'>
      <input type='text' name='gfunc2' maxLength='10' />
      <label>
        <input
          type='radio'
          name={`inex${props.num}`}
          value='inclui'
          id='inclui'
        />
        Incluir
      </label>
      <label>
        <input
          type='radio'
          name={`inex${props.num}`}
          value='incsom'
          id='exclui'
        />
        Incluir simultâneo
      </label>
      <label>
        <input
          type='radio'
          name={`inex${props.num}`}
          value='exclui'
          id='exclui'
        />
        Excluir
      </label>
    </div>
  );
}
