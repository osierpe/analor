/* eslint-disable no-eval */
export default function FormAnalise(props) {
  // TODO: passar tudo para componentes separados

  //  Elementos
  if (props.item.tipo === 'possui') {
    const elementosEl = props.item.elementos.map(el => {
      return (
        <div className='elementos__input' key={el}>
          <label htmlFor={`tem${el}`}>
            Possui {el}
            <input
              className='elementos__input--checkbox'
              type='checkbox'
              id={`tem${el}`}
              name={`tem${el}`}
              checked={eval(`props.formData.tem${el}`)}
            />
          </label>
          <label htmlFor={`quant${el}`}>
            Quantidade de {el}:
            <input
              type='number'
              className='elementos__input--número'
              id={`quant${el}`}
              name={`quant${el}`}
              min='0'
              value={eval(`props.formData.quant${el}`)}
            />
          </label>
        </div>
      );
    });

    return (
      <section className='elementos'>
        <h2 className='elementos__título'>Elementos</h2>
        <div className='elementos__inputBox'>{elementosEl}</div>
        <div className='tooltip'>
          <h3>Modo de busca por {props.item.nome.toLowerCase()}</h3>
          <p className='tooltip__text'>{props.item.descrição}</p>
        </div>
      </section>
    );
  }

  //   Propriedades
  else if (props.item.tipo === 'alcance') {
    const propriedadesEl = props.item.propFisQui.map(fisQui => {
      const camelCaseName = `${fisQui.charAt(0).toLowerCase()}${fisQui
        .slice(1)
        .replace('de ', '')
        .replace(' ', '')}`;

      return (
        <div className='propriedades__input' key={fisQui}>
          <label htmlFor={fisQui.replace(' ', '')}>{fisQui}</label>
          <input
            id={camelCaseName}
            type='number'
            step='0.1'
            min='0'
            name={`${camelCaseName}Min`}
            value={eval(`props.formData.${camelCaseName}Min`)}
          />
          à{' '}
          <input
            type='number'
            min='0'
            step='0.1'
            name={`${camelCaseName}Max`}
            value={eval(`props.formData.${camelCaseName}Max`)}
          />
        </div>
      );
    });
    return (
      <section className='propriedades'>
        <h2 className='propriedades__título'>Propriedades</h2>
        <div className='propriedades__inputBox'>{propriedadesEl}</div>
      </section>
    );
  }

  //   ecgf
  else if (props.item.tipo === 'incluir/excluir') {
    // criando 6 elementos de de ecgf
    const ecgfEl = [];
    for (let i = 1; i < 7; i++) {
      ecgfEl.push(
        <div className='ecgf__input' key={`gFunc${i}`}>
          <input
            type='text'
            name={`gFunc${i}`}
            maxLength='10'
            value={eval(`props.formData.gFunc${i}`)}
            checked={eval(``)}
          />
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
      <section className='ecgf'>
        <h2 className='ecgf__título'>Grupo Funcional / Esqueleto de Carbono</h2>
        <div className='ecgf__inputBox'>{ecgfEl}</div>
        <div className='tooltip'>
          <h3>
            Modo de uso para busca por {props.item.nome.toUpperCase()}{' '}
            (Referencias)
          </h3>
          <p className='tooltip__text'>{props.item.descrição[0]}</p>
        </div>
        <div className='tooltip'>
          <h3>
            Modo de uso para busca por {props.item.nome.toUpperCase()} (Botões)
          </h3>
          <p className='tooltip__text'>{props.item.descrição[1]}</p>
        </div>
      </section>
    );
  }

  // CAS
  else if (props.item.tipo === 'texto') {
    return (
      <section className='cas'>
        <h2 className='cas__título'>{props.item.nome}</h2>
        <label>
          {props.item.nome}:
          <input
            type='text'
            name={props.item.nome.toLowerCase()}
            maxLength='15'
          />
        </label>
      </section>
    );
  }
}
