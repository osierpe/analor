export default function FormAnalise(props) {
  // TODO: passar tudo para componentes separados

  //  Elementos
  if (props.item.tipo === 'possui') {
    const elementosEl = props.item.elementos.map(el => {
      return (
        <div className='elementos__input' key={el}>
          <label htmlFor={`tem${el}`}>Possui {el}</label>
          <input type='checkbox' id={`tem${el}`} name={`tem${el}`} />
        </div>
      );
    });

    return (
      <section className='elementos'>
        <h2 className='elementos__título'>Elementos</h2>
        {elementosEl}
        <h3 className='tooltip'>
          Modo de busca por {props.item.nome.toLowerCase()}
        </h3>
        <p className='tooltip__text'>{props.item.descrição}</p>
      </section>
    );
  }

  //   Propriedades
  else if (props.item.tipo === 'alcance') {
    const propriedadesEl = props.item.propFisQui.map(fisQui => {
      const camelCaseName = `${fisQui.charAt(0).toLowerCase}${fisQui
        .slice(1)
        .replace(' ', '')}`;

      return (
        <div className='propriedades__form' key={fisQui}>
          <label htmlFor={fisQui.replace(' ', '')}>{fisQui}</label>
          <input
            id={camelCaseName}
            type='number'
            step='0.1'
            min='0'
            name={`${camelCaseName}Min`}
          />
          à{' '}
          <input
            type='number'
            min='0'
            step='0.1'
            name={`${camelCaseName}Max`}
          />
        </div>
      );
    });
    return (
      <div className='propriedades'>
        <h2 className='propriedades__título'>Propriedades</h2>
        <div className='propriedades__input'>{propriedadesEl}</div>
      </div>
    );
  }

  //   ecgf
  else if (props.item.tipo === 'incluir/excluir') {
    // criando 6 elementos de de ecgf
    const ecgfEl = [];
    for (let i = 1; i < 7; i++) {
      ecgfEl.push(
        <div className='ecgf__form' key={`gFunc${i}`}>
          <input type='text' name={`gFunc${i}`} maxLength='10' />
          <label>
            <input type='radio' name={`inex${i}`} id='inclui' value='incluir' />
            Incluir
          </label>
          <label>
            <input type='radio' name={`inex${i}`} id='incSim' value='incSim' />
            Incluir Simultâneo
          </label>
          <label>
            <input
              type='radio'
              name={`inex${i}`}
              id='excluir'
              value='excluir'
            />
            Excluir
          </label>
        </div>
      );
    }

    return (
      <div className='ecgf'>
        <h1 className='ecgf__título'>Grupo Funcional / Esqueleto de Carbono</h1>
        {ecgfEl}
        <h3 className='tooltip'>
          {' '}
          Modo de uso para busca por {props.item.nome.toUpperCase()}{' '}
          (Referencias)
        </h3>
        <p className='tooltip_text'>{props.item.descrição[0]}</p>
        <h3 className='tooltip'>
          {' '}
          Modo de uso para busca por {props.item.nome.toUpperCase()}{' '}
          (Referencias)
        </h3>
        <p className='tooltip_text'>{props.item.descrição[0]}</p>
        <h3 className='tooltip'>
          {' '}
          Modo de uso para busca por {props.item.nome.toUpperCase()} (Botões)
        </h3>
        <p className='tooltip_text'>{props.item.descrição[1]}</p>
      </div>
    );
  }

  // CAS
  else if (props.item.tipo === 'texto') {
    return (
      <div className='cas'>
        <h2 className='cas__título'>{props.item.nome}</h2>
        <label>
          {props.item.nome}:
          <input
            type='text'
            name={props.item.nome.toLowerCase()}
            maxLength='15'
          />
        </label>
      </div>
    );
  }
}
