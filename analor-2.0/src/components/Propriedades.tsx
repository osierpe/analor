import { form_props } from "../form";
import FisQui_Range from "./sub_components/FisQui_Range";


export default function Propriedades({form_data, set_form_data}: form_props) {

    return <div className="propriedades">

        <div className="propriedades__header">
            <h2>Quais são as propriedades físico-químicas da amostra</h2>
            <img src="/public/i-icon.svg" alt="ícone de informação" />
        </div>

        <div className="propriedades__body">
        <FisQui_Range form_data={form_data} set_form_data={set_form_data} nome="Peso Molecular" min={0}  />
        <FisQui_Range form_data={form_data} set_form_data={set_form_data}  nome="Ponto de Fusão"  
        />
        <FisQui_Range form_data={form_data} set_form_data={set_form_data} nome="Ponto de Ebulição"  />

        </div>
    </div>
}