import { useState } from "react"
import Header from "./components/Header"
import Navigation from "./components/Navigation"

import {Form_Data} from "./form"
import Elementos from "./components/Elementos"
import Submit_Btn from "./components/Submit_Btn"
import Propriedades from "./components/Propriedades"

function App() {

  const [cur_page, set_cur_page] = useState(0)
  const [form_data, set_form_data] = useState(new Form_Data())

  const handle_change = (event: Event) => {
    console.log(event.target)
    console.log("changed event")
  }

  function get_cur_page () {
      switch (cur_page) {

        case 0: return <Elementos form_data={form_data} handle_change={handle_change} set_form_data={set_form_data}/>
        case 1: return <Propriedades form_data={form_data} handle_change={handle_change} set_form_data={set_form_data}/>
        default: return null
      }
  }

  const handle_submit = () => {

    console.log("submit :)")
   }

  return (
    <>
    <Header/>
    <Navigation set_cur_page={set_cur_page}/>
    {get_cur_page()}
    <h1>Current page: {cur_page}</h1>
    <Submit_Btn handle_submit={handle_submit}/>
    </>
  )
}

export default App
