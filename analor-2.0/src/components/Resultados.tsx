import Molecula from "../classes/Molecula";


interface ResultadosProps {
    moleculas:Array<Molecula>
    set_cur_page:any
}

export default function Resultados(props:ResultadosProps) {

    function handleClick(e:React.MouseEvent<HTMLDivElement>) {
        console.log(e.target, typeof(e))
        const target = e.target as HTMLElement;
        
        if (target.classList.contains("expandirBtn")) {
            const exp = target.parentElement?.parentElement?.lastChild as HTMLElement;
            if (exp.classList.contains("hidden")) {
                exp.classList.remove("hidden")
            }
            else {
                exp.classList.add("hidden")
            }
        }
        else {
            const exp = target.parentElement?.parentElement?.parentElement?.lastChild as HTMLElement;
            if (exp.classList.contains("hidden")) {
                exp.classList.remove("hidden")
            }
            else {
                exp.classList.add("hidden")
            }
        }

        
        
    }

    const moleculas_elementos = props.moleculas.map((molecula) => {
        return(
            <div className="resultados__lista--item" key={Math.random()}>
                <div className="info_basica">
                <div className="nome">
                    <h3>Nome</h3>
                    <p>{molecula.ncom}</p>
                </div>

                <div className="pontoFusao">
                    <h3>PF</h3>
                    <p>{String(molecula.pf) !== "None" ? `${molecula.pf}°C` : "N.A"}</p>
                </div>
                <div className="pontoEbuliçao">
                    <h3>PE</h3>
                    <p>{String(molecula.pe) !== "None" ? `${molecula.pe}°C` : "N.A"}</p>
                </div>
                <div className="pesoMolecular">
                    <h3>PM</h3>
                    <p>{molecula.pmol}</p>
                </div>
                <div className="expandirBtn" onClick={(e) => handleClick(e)}>
                    <img src="./seta_baixo.svg" alt="seta apontando para baixo" />
                    <p>Expandir</p>
                </div>

                </div>

                <div className="resultados__lista--item-expandivel hidden">
                <div className="col-1">
                <div className="linha">
                    <h3>CAS</h3>
                    <p>{molecula.cas}</p>
                </div><div className="linha">
                    <h3>Fórmula Linear</h3>
                    <p>{molecula.flin}</p>
                </div><div className="linha">
                    <h3>SMILES</h3>
                    <p>{molecula.smiles}</p>
                </div><div className="linha">
                    <h3>Nome Linear</h3>
                    <p>{molecula.nlin}</p>
                </div>
                </div>
                <div className="col-2">

                <div className="linha">
                    <h3>Pfder</h3>
                    <p>{molecula.pfder}</p>
                </div>
                <div className="container-img">

                <img src={molecula.estrutura} alt="" />
                </div>
                </div>
                </div>
            </div>
        )
    })

    return (
        <div className="resultados">

        <header className="resultados__header">
            <div className="resultados__header--container">
                <div className="resultados__header--container-btn" onClick={() => {props.set_cur_page(0)}}>
                <img src="/seta.svg" alt="seta" />
                <h2>Voltar</h2>
                </div>

                <h1>Resultado</h1>
            </div>
        </header>
        <main>
        <div className="resultados__lista">
                    {moleculas_elementos}
                </div>
        </main>
        </div>
    )
}