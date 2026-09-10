<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>pinjam.in — Masuk</title>

<style>
  :root{
    --cream:#f4f1ea;
    --cream-deep:#efebe1;
    --teal-dark:#0f6b64;
    --teal:#1a7a72;
    --teal-deep:#0a4a45;
    --ink:#1c2b29;
    --muted:#7d8a87;
    --card-border:#e4e0d6;
  }

  *{
    box-sizing:border-box;
  }

  body{
    margin:0;
    font-family:'Segoe UI', system-ui, -apple-system, sans-serif;
    background:var(--cream-deep);
    color:var(--ink);
  }

  .page{
    width:100%;
    min-height:100vh;
    display:flex;
    flex-direction:column;
    background:var(--cream);
  }

  /* NAVBAR */
  nav{
    display:flex;
    align-items:center;
    padding:18px 56px;
    background:var(--cream);
  }

  .brand{
    display:flex;
    align-items:center;
    gap:10px;
  }

  .brand .logo{
    width:34px;
    height:34px;
    border-radius:8px;
    background:var(--teal);
    display:flex;
    align-items:center;
    justify-content:center;
  }

  .brand .logo svg{
    width:18px;
    height:18px;
  }

  .brand-text{
    line-height:1.1;
  }

  .brand-text .name{
    font-weight:800;
    font-size:16px;
    color:var(--ink);
  }

  .brand-text .tag{
    font-size:9px;
    letter-spacing:1px;
    color:var(--teal);
    font-weight:700;
  }

  /* MAIN */
  main{
    flex:1;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:40px 60px;
    gap:100px;
    position:relative;
    overflow:hidden;
  }

  .left{
    position:relative;
    z-index:1;
    max-width:520px;
  }

  .ring{
    position:absolute;
    left:-40px;
    top:50%;
    transform:translateY(-50%);
    width:340px;
    height:340px;
    border-radius:50%;
    border:1px solid #e2ddd0;
    z-index:0;
  }

  .left h1{
    font-size:44px;
    line-height:1.15;
    margin:0;
    position:relative;
    z-index:1;
  }

  .left h1 .l1{
    display:block;
    font-weight:800;
    color:var(--teal-deep);
  }

  .left h1 .l2{
    display:block;
    font-weight:400;
    font-family:Georgia, 'Times New Roman', serif;
    color:var(--teal);
    font-style:italic;
    margin-top:4px;
  }

  /* CARD */
  .card{
    background:var(--cream);
    border:1px solid var(--card-border);
    border-radius:16px;
    box-shadow:0 20px 45px rgba(15,45,42,0.10);
    padding:36px 38px 30px;
    width:100%;
    max-width:340px;
    position:relative;
    z-index:1;
  }

  .card h2{
    margin:0 0 6px;
    font-size:21px;
    font-weight:800;
    color:var(--ink);
  }

  .card .subtitle{
    margin:0 0 22px;
    font-size:12.5px;
    color:var(--muted);
  }

  label{
    display:block;
    font-size:12.5px;
    font-weight:700;
    color:var(--ink);
    margin-bottom:6px;
  }

  .field{
    display:flex;
    align-items:center;
    gap:8px;
    border:1px solid var(--card-border);
    background:#fff;
    border-radius:8px;
    padding:10px 12px;
    margin-bottom:16px;
  }

  .field:focus-within{
    border-color:var(--teal);
    box-shadow:0 0 0 3px rgba(26,122,114,0.12);
  }

  .field svg{
    width:16px;
    height:16px;
    color:var(--muted);
    flex-shrink:0;
  }

  .field input{
    border:none;
    outline:none;
    flex:1;
    font-size:13.5px;
    background:transparent;
    color:var(--ink);
    font-family:inherit;
  }

  .field input::placeholder{
    color:#a9b0ae;
  }

  .toggle-eye{
    cursor:pointer;
    color:var(--muted);
    display:flex;
  }

  .btn-submit{
    width:100%;
    padding:9px;
    border:none;
    border-radius:8px;
    background:var(--teal-dark);
    color:#fff;
    font-weight:700;
    font-size:13.5px;
    cursor:pointer;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    margin-top:4px;
  }

  .btn-submit:hover{
    background:var(--teal-deep);
  }

  .divider{
    display:flex;
    align-items:center;
    gap:10px;
    margin:18px 0 14px;
    color:var(--muted);
    font-size:11px;
  }

  .divider::before,
  .divider::after{
    content:"";
    flex:1;
    height:1px;
    background:var(--card-border);
  }

  .signup-line{
    text-align:center;
    font-size:12.5px;
    color:var(--muted);
  }

  .signup-line a{
    color:var(--teal-deep);
    font-weight:700;
    text-decoration:none;
  }

  .signup-line a:hover{
    text-decoration:underline;
  }

  footer{
    background:var(--cream-deep);
    height:56px;
  }

  @media (max-width:900px){
    main{
      flex-direction:column;
      padding:40px 24px;
      text-align:center;
    }

    .left{
      max-width:100%;
    }

    .left h1{
      font-size:34px;
    }

    .ring{
      display:none;
    }

    .card{
      margin:0 auto;
    }
  }
</style>
</head>

<body>

<div class="page">

  <nav>
    <div class="brand">

      <div class="logo">
        <svg viewBox="0 0 24 24" fill="none">
          <rect x="3" y="3" width="7" height="7" rx="1.5" fill="#fff"/>
          <rect x="14" y="3" width="7" height="7" rx="1.5" fill="#fff" opacity="0.7"/>
          <rect x="3" y="14" width="7" height="7" rx="1.5" fill="#fff" opacity="0.7"/>
          <rect x="14" y="14" width="7" height="7" rx="1.5" fill="#fff"/>
        </svg>
      </div>

      <div class="brand-text">
        <div class="name">pinjam.in</div>
        <div class="tag">SISTEM PEMINJAMAN ALAT</div>
      </div>

    </div>
  </nav>


  <main>

    <div class="left">

      <div class="ring"></div>

      <h1>
        <span class="l1">Masuk untuk</span>
        <span class="l2">pinjam alat.</span>
      </h1>

    </div>

    <form
      class="card"
      method="POST"
      action="../controller/c_login.php"
    >

      <h2>Selamat datang kembali</h2>

      <p class="subtitle">
        Masuk ke akunmu untuk mulai meminjam alat.
      </p>


      <label for="username">
        Username
      </label>

      <div class="field">

        <svg
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path d="M4 6l8 6 8-6"/>
          <rect x="3" y="5" width="18" height="14" rx="2"/>
        </svg>

        <input
          id="username"
          name="username"
          type="text"
          placeholder="Username"
          autocomplete="username"
          required
          autofocus
        >

      </div>


      <label for="password">
        Kata sandi
      </label>

      <div class="field">

        <svg
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <rect x="5" y="11" width="14" height="9" rx="2"/>
          <path d="M8 11V8a4 4 0 0 1 8 0v3"/>
        </svg>

        <input
          id="password"
          name="password"
          type="password"
          placeholder="Masukkan kata sandi"
          autocomplete="current-password"
          required
        >

        <span
          class="toggle-eye"
          onclick="togglePw()"
        >
          <svg
            id="eyeIcon"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
            <circle cx="12" cy="12" r="3"/>
          </svg>
        </span>

      </div>


      <button
        class="btn-submit"
        type="submit"
      >
        Masuk

        <svg
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2.4"
          style="width:14px;height:14px;"
        >
          <path d="M5 12h14M13 6l6 6-6 6"/>
        </svg>

      </button>


      <div class="divider">
        atau
      </div>


      <p class="signup-line">
        Belum punya akun?
        <a href="v_register.php">
          Daftar sekarang
        </a>
      </p>

    </form>

  </main>


  <footer></footer>

</div>


<script>

function togglePw(){

    const pw = document.getElementById('password');
    const eye = document.getElementById('eyeIcon');

    if(pw.type === 'password'){

        pw.type = 'text';

        eye.innerHTML =
        '<path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a18.6 18.6 0 0 1 5.06-5.94M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 7 11 7a18.6 18.6 0 0 1-2.16 3.19M14.12 14.12a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/>';

    } else {

        pw.type = 'password';

        eye.innerHTML =
        '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/>';

    }

}

</script>

</body>
</html>