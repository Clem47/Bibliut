import React from 'react';
import 'semantic-ui-css/semantic.min.css';
import Footer from './Footer';
import Header from './Header';
import Results from './Results';
import Recherche from './Recherche';

export default function SearchPage() {
    return (<div style={{ display : "flex", flexDirection : "column", minHeight : "100vh"}}>
                <div style={{}}>
                    <Recherche />
                </div>
                <div style={{ marginTop : "auto" }}>
                    <Results />
                </div>
                <div style={{ marginTop : "auto" }}>
                    <Footer />
                </div>
            </div>
            );
            
}