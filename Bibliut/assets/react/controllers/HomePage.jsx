import React from 'react';
import 'semantic-ui-css/semantic.min.css';
import Recherche from './Recherche';
import Footer from './Footer';
import NewBooks from './NewBooks';
import LastLoans from './LastLoans';
import { Header } from 'semantic-ui-react';

export default function HomePage() {
    const authed = localStorage.getItem('token') && localStorage.getItem('token') !== 'undefined';
    return (<div style={{ display : "flex", flexDirection : "column", minHeight : "100vh"}}>
                <div style={{ marginBottom: "auto" }}>
                    <Recherche />
                </div>
                { authed ?
                <div style={{ marginTop : "auto" }}>
                <div style={{ marginTop : "auto" }}>
                    <LastLoans />
                </div>
                <div>
                    <NewBooks />
                </div>
                </div>
                : <Header as = 'h1' textAlign = 'center' > Bienvenue sur la bibliothèque de l'IUT ! </Header>
                }
                <div style={{ marginTop : "auto" }}>
                    <Footer />
                </div>
            </div>
            );
}