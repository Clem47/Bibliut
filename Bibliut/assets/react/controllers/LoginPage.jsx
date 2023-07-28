import React from 'react';
import 'semantic-ui-css/semantic.min.css';
import Footer from './Footer';
import Header from './Header';
import Connexion from './Connexion';


export default function HomePage() {
    return (<>
            <Header />
            <Connexion />
            <Footer />
            </>
            );
}