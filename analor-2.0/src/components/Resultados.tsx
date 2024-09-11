import React, { useState } from "react";
import Molecula from "../classes/Molecula";


interface ResultadosProps {
    moleculas:Array<Molecula>
}

export default function Resultados(props:ResultadosProps) {
    
    const moleculas_elementos = props.moleculas.map((molecula) => {
        return(
            <div className="resultados__lista--item">
                <div className="nome">
                    <h3>Nome</h3>
                    <p>{molecula.ncom}</p>
                </div>

                <div className="pontoFusao">
                    <h3>PF</h3>
                    <p>{molecula.pf}</p>
                </div>
                <div className="pontoEbuliçao">
                    <h3>PE</h3>
                    <p>{molecula.pe}</p>
                </div>
                <div className="pesoMolecular">
                    <h3>PM</h3>
                    <p>{molecula.pmol}</p>
                </div>
                <div className="expandirBtn">
                    <img src="seta_baixo" alt="seta apontando para baixo" />
                    <p>Expandir</p>
                </div>

                <div className="resultados__lista--item-expandivel">
                <div className="cas">
                    <h3>CAS</h3>
                    <p>{molecula.cas}</p>
                </div><div className="fLin">
                    <h3>Fórmula Linear</h3>
                    <p>{molecula.flin}</p>
                </div><div className="smiles">
                    <h3>SMILES</h3>
                    <p>{molecula.smiles}</p>
                </div><div className="NomeLinear">
                    <h3>Nome Linear</h3>
                    <p>{molecula.nlin}</p>
                </div>
                <div className="Pfder">
                    <h3>Pfder</h3>
                    <p>{molecula.pfder}</p>
                </div>
                <img src={molecula.estrutura} alt="" />
                </div>
            </div>
        )
    })

    return (
        <>
        <header className="resultados__header">
            <div className="resultados__header--container">
                <div className="resultados__header--container-btn">
                <img src="/seta.svg" alt="seta" />
                <h2>Voltar</h2>
                </div>
                <div className="resultados__lista">
                    {moleculas_elementos}
                </div>
                <h1>Resultado</h1>
            </div>
        </header>
        <main>

        </main>
        </>
    )
}