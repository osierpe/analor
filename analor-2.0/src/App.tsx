import { useState } from 'react'
import { Form_Data } from './form'

import Header from './components/Header'
import Navigation from './components/Navigation'
import Elementos from './components/Elementos'
import Submit_Btn from './components/Submit_Btn'
import Propriedades from './components/Propriedades'
import Grupo_Funcional from './components/Grupo_Funcional'
import Identificadores from './components/Identificadores'

import './Sass/styles.css'

function App() {
  const [cur_page, set_cur_page] = useState(0)
  const [form_data, set_form_data] = useState(new Form_Data())
  const [cur_ecgf_displaying, set_cur_ecgf_displaying] = useState(2)

  function get_cur_page() {
    switch (cur_page) {
      case 0:
        return <Elementos form_data={form_data} set_form_data={set_form_data} />
      case 1:
        return (
          <Propriedades form_data={form_data} set_form_data={set_form_data} />
        )
      case 2:
        return (
          <Grupo_Funcional
            form_data={form_data}
            set_form_data={set_form_data}
            cur_displaying={cur_ecgf_displaying}
            set_cur_displaying={set_cur_ecgf_displaying}
          />
        )
      case 3:
        return (
          <Identificadores
            form_data={form_data}
            set_form_data={set_form_data}
          />
        )
      default:
        return null
    }
  }

  const handle_submit = () => {
    console.log('submit :)')
  }

  return (
    <>
      <Header />
      <div className="content">
        <Navigation set_cur_page={set_cur_page} cur_page={cur_page} />
        <main>{get_cur_page()}</main>
        <Submit_Btn handle_submit={handle_submit} />
      </div>
    </>
  )
}

export default App
