export default function FisQui(props) {
    const propriedadesEl = props.formData.propriedades.map((fisQui , index) => {
        const camelCaseName = `${fisQui.nome.charAt(0).toLowerCase()}${fisQui.nome
          .slice(1)
          .replace('de ', '')
          .replace(' ', '')}`;
  
        return (
          <div className='propriedades__input' key={fisQui.nome}>
            <label htmlFor={camelCaseName}>{fisQui.nome}</label>
            <input
              id={camelCaseName}
              type='number'
              step='1'
              pattern="[0-9]{10}"
              min= {fisQui.nome === 'Peso Molecular' ? '0' : '-273'}
              name={`${fisQui.nome}-min`}
              value={props.formData.propriedades[index].alcance[0]}
              onChange={props.handleChange}
            />
            à{' '}
            <input
              type='number'
              min='0'
              step='1'
              name={`${fisQui.nome}-max`}
              pattern="[0-9]{10}"
              value={props.formData.propriedades[index].alcance[1]}
              onChange={props.handleChange}
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