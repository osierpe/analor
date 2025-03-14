import { useState, useEffect } from 'react'
import { Form_Data } from './form'

import Header from './components/Header'
import Navigation from './components/Navigation'
import Elementos from './components/Elementos'
import Propriedades from './components/Propriedades'
import Grupo_Funcional from './components/Grupo_Funcional'
import Identificadores from './components/Identificadores'
import Resultados from './components/Resultados'

import './Sass/styles.css'
import Molecula from './classes/Molecula'
import { moleculaTeste } from './classes/Molecula'


export class Abrev {
  nome: string
  nlin: string

  constructor(nome: string, abrev: string) {
    this.nome = nome
    this.nlin = abrev
  }
}


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
  const [curPage, setCurPage] = useState(0)
  const [formData, setFormData] = useState(new Form_Data())
  const [curDisplayingEcgf, setCurDisplayingEcgf] = useState(2)
 const [moleculas, setMoleculas] = useState<Molecula[]>([])
  function getCurPage() {
    switch (curPage) {
      case 0:
        return <Elementos form_data={formData} set_form_data={setFormData} />
      case 1:
        return (
          <Propriedades form_data={formData} set_form_data={setFormData} />
        )
      case 2:
        return (
          <Grupo_Funcional
            form_data={formData}
            set_form_data={setFormData}
            cur_displaying={curDisplayingEcgf}
            set_cur_displaying={setCurDisplayingEcgf}
            is_mobile={isMobile}
            abrevs={abrevs}
          />
        )
      case 3:
        return (
          <Identificadores
            form_data={formData}
            set_form_data={setFormData}
          />
        )
      case 4:
        console.log(moleculas)
        return(
          <Resultados moleculas={getMoleculas()} set_cur_page={setCurPage}/>
        )
      default:
        return null
    }
  }
  function getMoleculas() {
    return moleculas
  }

  

  const getAbrevs = async (): Promise<Abrev[]> => {
    try {
      const response = await fetch("http://localhost:5000/abrev");
      if (!response.ok) {
        console.error("Error fetching data");
        return [];
      }
      const data = await response.json();
      console.log(data);
      const abrevs: Abrev[] = [];
      data.forEach((item: { nlin: string; nome: string }) => {
        abrevs.push(new Abrev(item.nome, item.nlin));
      });
      return abrevs;
    } catch (error) {
      console.error("Error sending request:", error);
      return [];
    }
  };

  const handleSubmit = async (event:any) => {
    
    event.preventDefault()
    
  const formDataJson = JSON.stringify(formData);
  const queryParams = new URLSearchParams({ data: formDataJson }).toString();

  try {
    const response = await fetch(`http://localhost:5000/search?${queryParams}`)

    if (!response.ok) {
      setMoleculas(() => {
        const newMoleculas = [];
        for (let i = 0; i < 10; i++) {
          newMoleculas.push(moleculaTeste);
          console.log(newMoleculas, i);
        }
        return newMoleculas;
      });
      throw new Error(`HTTP error! Status: ${response.status}`);
    }
    else {
      const responseData = await response.json();
      console.log("Resposta: ", responseData);
      setMoleculas([]);
      responseData.forEach((molecula:any) => {
          const _molecula = new Molecula(
            molecula.cas,
            molecula.niup,
            molecula.ncom,
            molecula.ningles,
            molecula.nlin,
            molecula.flin,
            molecula.pmol,
            molecula.fmol,
            molecula.carb,
            molecula.hidr,
            molecula.oxig,
            molecula.nitr,
            molecula.enxo,
            molecula.clor,
            molecula.brom,
            molecula.iodo,
            molecula.fluo,
            molecula.pf,
            molecula.pfcarac,
            molecula.pe,
            molecula.pecarac,
            molecula.pfder,
            [
              molecula.iv1, molecula.iv2, molecula.iv3, molecula.iv4, molecula.iv5,
              molecula.iv6, molecula.iv7, molecula.iv8, molecula.iv9, molecula.iv10,
              molecula.iv11, molecula.iv12, molecula.iv13, molecula.iv14, molecula.iv15,
              molecula.iv16, molecula.iv17, molecula.iv18, molecula.iv19, molecula.iv20
            ]
            ,
            [
              molecula.iv1car, molecula.iv2car, molecula.iv3car, molecula.iv4car, molecula.iv5car,
              molecula.iv6car, molecula.iv7car, molecula.iv8car, molecula.iv9car, molecula.iv10car,
              molecula.iv11car, molecula.iv12car, molecula.iv13car, molecula.iv14car, molecula.iv15car,
              molecula.iv16car, molecula.iv17car, molecula.iv18car, molecula.iv19car, molecula.iv20car
            ]
            ,
  
            [
              molecula.ms1, molecula.ms2, molecula.ms3, molecula.ms4, molecula.ms5,
              molecula.ms6, molecula.ms7, molecula.ms8
            ],
            [
              molecula.ms1car, molecula.ms2car, molecula.ms3car, molecula.ms4car, molecula.ms5car,
              molecula.ms6car, molecula.ms7car, molecula.ms8car
            ],
            [
              molecula.ims1, molecula.ims2, molecula.ims3, molecula.ims4, molecula.ims5,
              molecula.ims6, molecula.ims7, molecula.ims8
            ],
            [
              molecula.ims1car, molecula.ims2car, molecula.ims3car, molecula.ims4car, molecula.ims5car,
              molecula.ims6car, molecula.ims7car, molecula.ims8car
            ]
            ,
            molecula.smiles,
            molecula.ecgf,
            molecula.estrutura,
            molecula.no_ifrj)
  
            setMoleculas((prevMol) => {
              const newMol = [...prevMol];
              newMol.push(_molecula);
              return newMol;
            })
        }
      )
    }
   

  } catch (error) {
    console.error("Error sending request:", error);
  }
    setCurPage(4)
  }

  const abrevs = getAbrevs()  
  console.log(abrevs)
  return (
    <>
      <Header />
      <div className="content">
        {curPage !== 4 ? <Navigation set_cur_page={setCurPage} cur_page={curPage} /> : null}
        {curPage !== 4 ? <main>{getCurPage()}</main> : getCurPage()}
        { curPage === 4 ? null : <div className="submit_btn_container">
          {isMobile ? (
            curPage !== 0 ? (
              <img
                onClick={() => setCurPage(curPage - 1)}
                className="left-btn"
                src="/btn_esquerda.svg"
              />
            ) : (
              <div className="left-btn"></div>
            )
          ) : null}
          <button type="submit" onClick={(e) => handleSubmit(e)}>
            <img src="/atomo.svg" alt="símbolo de átomo" />
            Mostrar Resultado
          </button>
          {isMobile ? (
            curPage !== 3 ? (
              <img
                onClick={() => setCurPage(curPage + 1)}
                className="right-btn"
                src="/btn_direita.svg"
              />
            ) : (
              <div className="right-btn"></div>
            )
          ) : null}
        </div>}
      </div>
    </>
  )
}

export default App
