export default function Elementos(props) {

    const elementosEl = props.formData.elementos.map((el, index) => {
        return (
      <div className='elementos__input' key={el.nome}>
        <label htmlFor={`tem${el.nome}`}>
          Possui {el.nome}
          <input
            className='elementos__input--checkbox'
            type='checkbox'
            id={`tem${el.nome}`}
            name={`${el.nome}-tem`}
            checked={props.formData.elementos[index].tem}
            onChange={props.handleChange}
            />
        </label>
        <label htmlFor={`quant${el.nome}`}>
          Quantidade de {el.nome}:
          <input
            type='number'
            className='elementos__input--número'
            id={`quant${el.nome}`}
            name={`${el.nome}-quantidade`}
            min='0'
            value={(props.formData.elementos[index].quantidade)}
            onChange={props.handleChange}
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
        <h3>Modo de busca por elementos</h3>
        <p className='tooltip__text'>Marque a caixa "possui" dos elementos que tenha certeza de sua
          presença e/ou sua quantidade se souber. Caso não saiba se há ou não
          presença, deixe os campos deste elemento em branco. Caso tenha certeza
          de que não há presença, digite sua quantidade = 0, e deixe a caixa
          "possui" desmarcada.</p>
      </div>
    </section>
  );
}