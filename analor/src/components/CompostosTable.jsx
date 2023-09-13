import { useState } from "react";

export default function CompostosTable (props) {

    console.log(props.compostos)

    const rowsPerPage = 30;
  const [currentPage, setCurrentPage] = useState(1);

  const totalPages = Math.ceil(props.compostos.length / rowsPerPage);

  const getCurrentPageData = () => {
    const startIndex = (currentPage - 1) * rowsPerPage;
    const endIndex = startIndex + rowsPerPage;
    return props.compostos.slice(startIndex, endIndex);
  };

  const handlePageChange = (newPage) => {
    setCurrentPage(newPage);
  };

  const substituirNone = (str) => {
    return str === 'None' ? 'N/A' : str
  }

    return (
        <div className="resultados">

        <table className="resultados__table">
             <tbody>
                <tr className="propriedades">
                    <th colSpan="7" className="propriedades__item">Identificadores</th>
                    <th colSpan="3" className="propriedades__item">Propriedades físicas</th>
                    <th colSpan="2" className="propriedades__item">Dados Espectometricos</th>
                </tr>
                <tr className="legendas">
                <th>Nome comum</th>
                <th>CAS</th>
                <th>SMILES</th>
                <th>Nome Linear(ECGF)</th>
                <th>Formula Linear</th>
                <th>FM</th>
                <th>PM</th>
                <th>PF</th>
                <th>Peb</th>
                <th>Pfder</th>
                <th>IV</th>
                <th>EM</th>
                </tr>

                {getCurrentPageData().map(molecula => {
                    return (<tr key={molecula.ncom}> 
                        <td>{molecula.ncom}</td>
                        <td>{molecula.cas}</td>
                        <td>{molecula.smiles}</td>
                        <td>{molecula.ecgf}</td>
                        <td>{molecula.flin}</td>
                        <td>{molecula.fmol}</td>
                        <td>{molecula.pmol}</td>
                        <td>{substituirNone(molecula.pf)}</td>
                        <td>{substituirNone(molecula.pe)}</td>
                        <td>{substituirNone(molecula.pfder)}</td>
                        <td>IV PLACEHOLDER</td>
                        <td>EV PLACEHOLDER</td>
                    </tr>)
                })}
             </tbody>
        </table>
         <div className="controles">
         <button id="voltar" onClick={() => handlePageChange(currentPage - 1)} disabled={currentPage === 1}>
           Anterior
         </button>
         <span id="texto">
           Pagina {currentPage} de {totalPages}
         </span>
         <button id="avançar"onClick={() => handlePageChange(currentPage + 1)} disabled={currentPage === totalPages}>
           Próximo
         </button>
       </div>
       </div>

    )
}