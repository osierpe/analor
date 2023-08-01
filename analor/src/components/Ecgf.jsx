export default function Ecgf(props) {

const ecgfEl = props.formData.ecgf.map((gf, index) => {
  return(
    <div className='ecgf__input' key={`gFunc${index}`}>
      <input
        type='text'
        name={`gFunc${index}`}
        maxLength='10'
        value={gf.gFunc}
        onChange={props.handleChange}
      />
      <label>
        <input
          type='radio'
          name={`inex${index}`}
          id='incluir'
          value='incluir'
          onChange={props.handleChange}
          checked={gf.inex === 'incluir'}
        />
        Incluir
      </label>
      <label>
        <input
          type='radio'
          name={`inex${index}`}
          id='incSim'
          value='incSim'
          onChange={props.handleChange}
          checked={gf.inex === 'incSim'}
        />
        Incluir Simultâneo
      </label>
      <label>
        <input
          type='radio'
          name={`inex${index}`}
          id='excluir'
          value='excluir'
          onChange={props.handleChange}
          checked={gf.inex === 'excluir'}
        />
        Excluir
      </label>
    </div>)
  });
  
  return (
  <section className='ecgf'>
    <h2 className='ecgf__título'>Grupo Funcional / Esqueleto de Carbono</h2>
    <div className='ecgf__inputBox'>{ecgfEl}</div>
    <div className='tooltip'>
      <h3>
        Modo de uso para busca por ECGF{' '}
        (Referencias)
      </h3>
      <p className='tooltip__text'>Colocar na área de texto a abreviação referente ao grupo funcional (a
          referência a abreviação fica ao lado do botão "aplicar filtro")</p>
    </div>
    <div className='tooltip'>
      <h3>
        Modo de uso para busca por ECGF (Botões)
      </h3>
      <p className='tooltip__text'>Incluir: seleciona as moléculas com os grupos assinalados (não
          necessariamente na mesma molécula) Incluir simultâneo: seleciona as
          moléculas com os grupos assinalados (na mesma molécula) Excluir:
          elimina as moléculas com os grupos assinalados.</p>
    </div>
  </section>
);
}