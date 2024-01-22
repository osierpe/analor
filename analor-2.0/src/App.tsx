import { useState, useEffect } from 'react'
import { Form_Data } from './form'

import Header from './components/Header'
import Navigation from './components/Navigation'
import Elementos from './components/Elementos'
import Propriedades from './components/Propriedades'
import Grupo_Funcional from './components/Grupo_Funcional'
import Identificadores from './components/Identificadores'

import './Sass/styles.css'

function App() {
  const handleResize = () => {
    setIsMobile(window.innerWidth <= 600)
  }

  useEffect(() => {
    window.addEventListener('resize', handleResize)
    return () => {
      window.removeEventListener('resize', handleResize)
    }
  }, [])

  const [isMobile, setIsMobile] = useState(window.innerWidth <= 600)
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

  const handle_submit = async (event: Event) => {
    event.preventDefault()

    const formDataJson = JSON.stringify(form_data)
    const queryParams = new URLSearchParams({ data: formDataJson }).toString()
    const response = await fetch(`http://localhost:5000/search?${queryParams}`)

    const data = await response.json()
    console.log(data)
  }

  return (
    <>
      <Header />
      <div className="content">
        <Navigation set_cur_page={set_cur_page} cur_page={cur_page} />
        <main>{get_cur_page()}</main>
        <div className="submit_btn_container">
          {isMobile ? (
            cur_page !== 0 ? (
              <h1
                onClick={() => set_cur_page(cur_page - 1)}
                className="left-btn"
              >
                btn esquerda
              </h1>
            ) : (
              <div className="left-btn"></div>
            )
          ) : null}
          <button type="submit" onClick={() => handle_submit}>
            <img src="/symbol-1.svg" alt="símbolo de átomo" />
            Mostrar Resultado
          </button>
          {isMobile ? (
            cur_page !== 3 ? (
              <h1
                onClick={() => set_cur_page(cur_page + 1)}
                className="right-btn"
              >
                btn direita
              </h1>
            ) : (
              <div className="right-btn"></div>
            )
          ) : null}
        </div>
      </div>
    </>
  )
}

export default App
