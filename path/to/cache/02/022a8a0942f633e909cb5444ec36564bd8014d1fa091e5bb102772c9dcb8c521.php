<?php

/* views/homepage.html.twig */
class __TwigTemplate_548fd7b8abd6a7dff993e4bf04c5748407cdf317b4898f1eb25eaaf01d8f5451 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("layout.html", "views/homepage.html.twig", 1);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "layout.html";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_body($context, array $blocks = array())
    {
        // line 4
        echo "    <h1>User List</h1>
    <ul>
        <li>Josh</li>
    </ul>
";
    }

    public function getTemplateName()
    {
        return "views/homepage.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  31 => 4,  28 => 3,  11 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "views/homepage.html.twig", "/home/lekz/boulot/42/web/matcha42/templates/views/homepage.html.twig");
    }
}
