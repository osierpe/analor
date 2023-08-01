import { useState } from 'react';
import Buttons from './components/Buttons';
import './sass/styles.css';
import Elementos from './components/Elementos';
import FisQui from './components/FisQui';
import Ecgf from './components/Ecgf';
import CompostosTable from './components/CompostosTable';

export default function App() {
	const [formData, setFormData] = useState({
		elementos: [
			{
				nome: 'Carbono',
				tem: '',
				quantidade: '',
			},
			{
				nome: 'Hidrogênio',
				tem: '',
				quantidade: '',
			},
			{
				nome: 'Nitrogênio',
				tem: '',
				quantidade: '',
			},
			{
				nome: 'Oxigênio',
				tem: '',
				quantidade: '',
			},
			{
				nome: 'Flúor',
				tem: '',
				quantidade: '',
			},
			{
				nome: 'Cloro',
				tem: '',
				quantidade: '',
			},
			{
				nome: 'Bromo',
				tem: '',
				quantidade: '',
			},
			{
				nome: 'Iodo',
				tem: '',
				quantidade: '',
			},
			{
				nome: 'Enxofre',
				tem: '',
				quantidade: '',
			},
		],
		propriedades: [
			{
				nome: 'Peso Molecular',
				alcance: ['', ''],
			},
			{
				nome: 'Ponto de Fusão',
				alcance: ['', ''],
			},
			{
				nome: 'Ponto de Ebulição',
				alcance: ['', ''],
			},
		],
		ecgf: [
			{
				gFunc: '',
				inex: 'incluir',
			},
			{
				gFunc: '',
				inex: 'incluir',
			},
			{
				gFunc: '',
				inex: 'incluir',
			},
			{
				gFunc: '',
				inex: 'incluir',
			},
			{
				gFunc: '',
				inex: 'incluir',
			},
			{
				gFunc: '',
				inex: 'incluir',
			},
		],

		cas: '',
	});

	const [resultados, setResultados] = useState(null);

	const handleChange = (e) => {
		const atualizarFormData = function (
			propType,
			parentKey,
			childKey,
			value,
			checked
		) {
			if (!childKey && propType !== 'cas') {
				const newArr = formData[propType].map((item, i) => {
					if (Number(parentKey.slice(-1)) === i) {
						const newItem = {
							gFunc: parentKey.slice(0, -1) === 'gFunc' ? value : item.gFunc,
							inex: parentKey.slice(0, -1) === 'inex' ? value : item.inex,
						};
						return newItem;
					} else {
						return item;
					}
				});

				setFormData((prevFormData) => {
					return {
						...prevFormData,
						[propType]: newArr,
					};
				});
			} else if (childKey) {
				const newArr = formData[propType].map((item) => {
					if (parentKey === item.nome) {
						let newItem;

						switch (childKey) {
							case 'tem':
								newItem = {
									nome: item.nome,
									tem: checked ? checked : '',
									quantidade: checked
										? item.tem === false
											? ''
											: item.quantidade
										: '',
								};
								console.log(item);
								break;

							case 'quantidade':
								newItem = {
									nome: item.nome,
									tem: value > 0,
									quantidade: value,
								};
								break;

							case 'min':
							case 'max':
								newItem = {
									nome: item.nome,
									alcance: [
										childKey === 'min'
											? value > item.alcance[1]
												? item.alcance[1]
												: value
											: item.alcance[0],
										childKey === 'max' ? value : item.alcance[1],
									],
								};
								break;

							default:
								newItem = item;
						}

						return newItem;
					}

					return item;
				});

				setFormData((prevFormData) => ({
					...prevFormData,
					[propType]: [...newArr],
				}));
			} else if (propType === 'cas') {
				setFormData((prevFormData) => ({
					...prevFormData,
					[propType]: value,
				}));
			}
		};
		const { name, value, checked } = e.target;
		const [parentKey, childKey] = name.split('-');
		if (childKey === 'tem' || childKey === 'quantidade') {
			atualizarFormData('elementos', parentKey, childKey, value, checked);
		} else if (childKey === 'min' || childKey === 'max') {
			atualizarFormData('propriedades', parentKey, childKey, value, checked);
		} else if (name.slice(0, -1) === 'gFunc' || name.slice(0, -1) === 'inex') {
			atualizarFormData('ecgf', parentKey, false, value);
		} else if (name === 'cas') {
			atualizarFormData('cas', false, false, value);
		}
	};

	const handleSubmit = async (event) => {
		event.preventDefault();

		const formDataJson = JSON.stringify(formData);
		const queryParams = new URLSearchParams({ data: formDataJson }).toString();
		const response = await fetch(`http://localhost:5000/search?${queryParams}`);

		const data = await response.json();
		setResultados(data);
	};

	return (
		<>
			<header>
				<h1 className='logo'>Analor</h1>
			</header>
			<main>
				<form onSubmit={handleSubmit}>
					<div className='analise'>
						<Elementos
							formData={formData}
							handleChange={handleChange}
						/>
						<FisQui
							formData={formData}
							handleChange={handleChange}
						/>
						<Ecgf
							formData={formData}
							handleChange={handleChange}
						/>
						<section className='cas'>
							<h2 className='cas__título'>CAS</h2>
							<label>
								CAS:
								<input
									type='text'
									name='cas'
									maxLength='15'
									value={formData.cas}
									onChange={handleChange}
								/>
							</label>
						</section>
					</div>
					<Buttons />
				</form>
				{resultados && <CompostosTable compostos={resultados}></CompostosTable>}
			</main>
		</>
	);
}
