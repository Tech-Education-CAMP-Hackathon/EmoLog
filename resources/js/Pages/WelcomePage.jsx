import React from 'react';
import { Link } from '@inertiajs/react';

export default function WelcomePage() {
    return (
        <div style={containerStyle}>
            <div style={contentStyle}>
                <img src="/images/Voice control-cuate 1.png" alt="Voice Recording Illustration" style={{ width: '300px', height: 'auto' }} />
                <h2>声で記録する私だけの日記</h2>
                <h1 style={{ fontSize: '50px', fontWeight: 'bold' }}>EmoLog</h1>
                <div style={{ marginTop: '20px' }}>
                    <Link href="/register" style={buttonStyle}>新規登録</Link>
                    <Link href="/login" style={buttonStyle}>ログイン</Link>
                </div>
            </div>
        </div>
    );
}

const containerStyle = {
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
    height: '100vh',
    textAlign: 'center',
};

const contentStyle = {
    maxWidth: '600px',
};

const buttonStyle = {
    padding: '10px 20px',
    margin: '10px',
    borderRadius: '20px',
    fontSize: '16px',
    color: 'white',
    backgroundColor: '#007bff',
    textDecoration: 'none',
    display: 'inline-block',
    cursor: 'pointer',
};
