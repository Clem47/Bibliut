import React from 'react';
import {Link}  from "react-router-dom";
import 'semantic-ui-css/semantic.min.css';
import { Button } from 'semantic-ui-react';
import { useNavigate } from "react-router-dom";


export default function Header() {
    const navigate = useNavigate();
    const logout = () => {
        localStorage.clear();
        navigate('/connexion');
    }
    return (<div class="header">
                <div class="top">
                    <div class="logo">
                        <h2>IUT - Bibliothèque</h2>
                    </div>
                    <div class="menu">
                        <ul>
                            <li><Link to="/">Accueil</Link></li>
                            <li><Link to="/friends">Amis</Link></li>
                            {localStorage.getItem('token') && localStorage.getItem('token') !== 'undefined' ?
                                <li><Button basic onClick={logout}>Logout</Button></li>
                                :
                                <li><Link to="/connexion">Connexion</Link></li>
                            }
                        </ul>
                    </div>
                </div>
            </div>);
}