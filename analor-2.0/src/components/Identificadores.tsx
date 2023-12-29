import { form_props } from '../form'

export default function Identificadores({
  form_data,
  set_form_data,
}: form_props) {
  const handle_change = function (event: any) {
    const value = event.target.value
    const name = event.target.name

    set_form_data((prev_form_data) => ({
      ...prev_form_data,
      [name]: value !== '' ? value : null,
    }))
  }

  return (
    <div className="identificadores">
      <div className="identificadores__header">
        <h2>Pesquise por compostos específicos.</h2>
        <img src="/i-icon.svg" alt="ícone de informação" />
      </div>
      <div className="identificadores__body">
        <label>
          <span className="identificadores__body--text">Cas:</span>
          <input
            type="text"
            name="cas"
            placeholder="cas"
            value={form_data.cas ? form_data.cas : ''}
            onChange={handle_change}
          />
        </label>
        <label>
          <span className="identificadores__body--text">Nome:</span>
          <input
            type="text"
            name="nome"
            placeholder="Nome do composto"
            value={form_data.nome ? form_data.nome : ''}
            onChange={handle_change}
          />
        </label>
      </div>
    </div>
  )
}
