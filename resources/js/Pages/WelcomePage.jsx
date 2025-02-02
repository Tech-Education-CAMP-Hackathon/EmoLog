import React from 'react';
import { Link } from '@inertiajs/react';

export default function WelcomePage() {
    return (
        <div style={containerStyle}>
            <div style={contentStyle}>
                <img
                    src="/images/Voice control-cuate 1.png"
                    alt="Voice Recording Illustration"
                    style={{ width: '300px', height: 'auto' }}
                />
                <h2 style={subheadingStyle}>音声で記録する、私だけの日記</h2>
                <h1 style={headingStyle}>EmoLog</h1>
                <div style={buttonContainerStyle}>
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
    backgroundColor: '#b4eeb4', // 背景色を変更
    height: '100vh',
    textAlign: 'center',
    padding: '20px',
};

const contentStyle = {
    maxWidth: '600px',
    padding: '20px',
    backgroundColor: 'white',  // 背景を白にしてコンテンツを目立たせる
    borderRadius: '20px',
    boxShadow: '0 4px 6px rgba(0, 0, 0, 0.1)',
};

const subheadingStyle = {
    fontSize: '20px',
    color: '#333',
    marginBottom: '20px',
};

const headingStyle = {
    fontSize: '60px', // 文字サイズを大きく
    fontWeight: 'bold',
    color: '#333',
    marginBottom: '40px',  // 余白を調整
    letterSpacing: '5px', // 文字間隔を少し広げてスタイリッシュに
};

const buttonContainerStyle = {
    display: 'flex',
    justifyContent: 'center',
    gap: '20px',  // ボタンの間隔
};

const buttonStyle = {
    padding: '12px 30px', // ボタンのサイズ調整
    borderRadius: '25px', // ボタンを丸くする
    fontSize: '18px',  // フォントサイズを少し大きく
    color: 'white',
    backgroundColor: '#007bff',
    textDecoration: 'none',
    display: 'inline-block',
    cursor: 'pointer',
    transition: 'background-color 0.3s ease', // ボタンにホバー効果を追加
};

buttonStyle[':hover'] = {
    backgroundColor: '#0056b3',  // ボタンのホバー時の色
};
