<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Connection</title>
</head>
<body class="font-inter bg-gray-100">
        <section class="flex flex-col items-center justify-center h-screen">
            <h1 class="text-3xl font-bold text-teal-700">création d'un nouveau compte</h1>
            
            <form method="POST" action="index.php?action=register" class="p-5 m-3 bg-teal-100 rounded-md shadow-md space-y-4 w-sm">
                <div class="flex justify-between items-center">
                    <label class="font-bold" for="username">Login :</label>
                    <input class="bg-white rounded-md border border-gray-300 p-2" type="text" id="username" name="username" required>
                </div>
                <div class="flex justify-between items-center">
                    <label class="font-bold" for="email">Email :</label>
                    <input class="bg-white rounded-md border border-gray-300 p-2" type="email" id="email" name="email" required>
                </div>
                <div class="flex justify-between items-center">
                    <label class="font-bold" for="password" >Mot de passe :</label>
                    <input class="bg-white rounded-md border border-gray-300 p-2" type="password" id="password" name="password" minlength="8" required>
                </div>
                <div class="flex justify-between items-center">
                    <label class="font-bold" for="password_confirm">Confirmer le mot de passe :</label>
                    <input class="bg-white rounded-md border border-gray-300 p-2" type="password" id="password_confirm" name="password_confirm" required>
                </div>
                <div>
                    <button class="bg-teal-500 text-white rounded-md p-2" type="submit">Se connecter</button>
                </div>
            </form>
            <?php if (isset($erreur) && $erreur): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($erreur); ?></span>
                </div>
            <?php endif; ?>
            <h2 class="mt-4">
                Vous avez déjà un compte ?
            <a class="text-teal-600 underline" href="index.php?action=login">Connectez-vous</a> 
        </h2>
        </section>
       
    
</body>
</html>
